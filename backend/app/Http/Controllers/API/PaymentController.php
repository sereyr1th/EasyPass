<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

/**
 * Payment Controller
 * Handles Stripe payment processing for ticket purchases
 */
class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key with validation
        $stripeSecret = config('services.stripe.secret');
        
        if (empty($stripeSecret) || $stripeSecret === 'sk_test_51234567890abcdef_test_your_secret_key_here') {
            \Log::error('Stripe secret key not configured properly. Please set STRIPE_SECRET in .env file.');
            throw new \Exception('Payment system not configured. Please contact administrator.');
        }
        
        Stripe::setApiKey($stripeSecret);
    }

    /**
     * Create payment intent for ticket purchase
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        \Log::info('Payment intent creation request received', $request->all());
        
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'amount' => 'required|numeric|min:0.50', // Stripe minimum is $0.50
            'quantity' => 'required|integer|min:1|max:10', // Allow 1-10 tickets per purchase
        ]);

        if ($validator->fails()) {
            \Log::error('Payment validation failed', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::find($request->event_id);

        // Check if event is available for purchase
        if (!$event->isAvailable()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event is not available for ticket purchase'
            ], 400);
        }

        // Check existing tickets for this user and event
        $existingTicketsCount = Ticket::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->count();

        // Prevent users from having more than 10 tickets total per event
        if ($existingTicketsCount + $request->quantity > 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only purchase up to 10 tickets per event. You currently have ' . $existingTicketsCount . ' tickets.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Generate unique transaction ID
            $transactionId = TicketPurchase::generateTransactionId();
            
            // Create Stripe PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => round($request->amount * 100), // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'event_id' => $event->id,
                    'user_id' => $request->user()->id,
                    'transaction_id' => $transactionId,
                    'event_title' => $event->title,
                    'user_email' => $request->user()->email,
                ],
                'description' => "Ticket for {$event->title}",
                'receipt_email' => $request->user()->email,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Create purchase record
            $purchase = TicketPurchase::create([
                'event_id' => $event->id,
                'user_id' => $request->user()->id,
                'ticket_id' => null, // Will be set when payment is completed
                'amount_paid' => $request->amount,
                'quantity' => $request->quantity,
                'payment_method' => 'stripe',
                'transaction_id' => $transactionId,
                'payment_status' => 'pending',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_client_secret' => $paymentIntent->client_secret,
                'stripe_metadata' => $paymentIntent->metadata->toArray(),
                'payment_expires_at' => now()->addMinutes(30) // Stripe allows longer timeout
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment intent created successfully',
                'data' => [
                    'purchase_id' => $purchase->id,
                    'transaction_id' => $transactionId,
                    'payment_method' => 'stripe',
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $request->amount,
                    'event' => $event,
                    'expires_at' => $purchase->payment_expires_at,
                    'status' => 'pending'
                ]
            ], 201);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollBack();
            \Log::error('Stripe API error', [
                'error' => $e->getMessage(),
                'type' => $e->getStripeCode(),
                'request' => $request->all()
            ]);
            $message = 'Payment service error';
            if (str_contains($e->getMessage(), 'Invalid API Key')) {
                $message = 'Payment system configuration error. Please contact administrator.';
            } elseif (str_contains($e->getMessage(), 'No API key provided')) {
                $message = 'Payment system not properly configured. Please contact administrator.';
            } else {
                $message = 'Payment service error: ' . $e->getMessage();
            }

            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment intent creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment intent',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        \Log::info('Payment status check request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:ticket_purchases,transaction_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $purchase = TicketPurchase::where('transaction_id', $request->transaction_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        // Check if payment has expired
        if ($purchase->payment_expires_at->isPast() && $purchase->payment_status === 'pending') {
            $purchase->update(['payment_status' => 'expired']);
            return response()->json([
                'status' => 'error',
                'message' => 'Payment has expired',
                'data' => ['payment_status' => 'expired']
            ], 400);
        }

        // If we have a Stripe PaymentIntent, check its status
        if ($purchase->stripe_payment_intent_id) {
            try {
                $paymentIntent = PaymentIntent::retrieve($purchase->stripe_payment_intent_id);
                
                // Update local status based on Stripe status
                if ($paymentIntent->status === 'succeeded' && $purchase->payment_status !== 'completed') {
                    $this->completePayment($purchase, $paymentIntent);
                } elseif ($paymentIntent->status === 'canceled') {
                    $purchase->update(['payment_status' => 'cancelled']);
                } elseif ($paymentIntent->status === 'payment_failed') {
                    $purchase->update(['payment_status' => 'failed']);
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                \Log::error('Failed to retrieve PaymentIntent from Stripe', [
                    'payment_intent_id' => $purchase->stripe_payment_intent_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'transaction_id' => $purchase->transaction_id,
                'payment_status' => $purchase->payment_status,
                'amount_paid' => $purchase->amount_paid,
                'created_at' => $purchase->created_at,
                'ticket' => $purchase->ticket ? $purchase->ticket->load('event') : null
            ]
        ]);
    }

    /**
     * Handle Stripe webhook
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            \Log::error('Invalid payload in Stripe webhook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            \Log::error('Invalid signature in Stripe webhook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        \Log::info('Stripe webhook received', ['type' => $event['type'], 'id' => $event['id']]);

        // Handle the event
        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object']);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event['data']['object']);
                break;
            case 'payment_intent.canceled':
                $this->handlePaymentIntentCanceled($event['data']['object']);
                break;
            default:
                \Log::info('Unhandled Stripe webhook event type: ' . $event['type']);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment intent
     */
    private function handlePaymentIntentSucceeded($paymentIntent): void
    {
        $purchase = TicketPurchase::where('stripe_payment_intent_id', $paymentIntent['id'])->first();

        if (!$purchase) {
            \Log::error('Purchase not found for PaymentIntent', ['payment_intent_id' => $paymentIntent['id']]);
            return;
        }

        if ($purchase->payment_status !== 'completed') {
            $this->completePayment($purchase, $paymentIntent);
        }
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed($paymentIntent): void
    {
        $purchase = TicketPurchase::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
        
        if ($purchase) {
            $purchase->update(['payment_status' => 'failed']);
            \Log::info('Payment marked as failed', ['transaction_id' => $purchase->transaction_id]);
        }
    }

    /**
     * Handle canceled payment intent
     */
    private function handlePaymentIntentCanceled($paymentIntent): void
    {
        $purchase = TicketPurchase::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
        
        if ($purchase) {
            $purchase->update(['payment_status' => 'cancelled']);
            \Log::info('Payment marked as cancelled', ['transaction_id' => $purchase->transaction_id]);
        }
    }

    /**
     * Complete payment and create ticket
     */
    private function completePayment(TicketPurchase $purchase, $paymentIntent): void
    {
        try {
            DB::beginTransaction();

            // Create tickets based on quantity
            $tickets = [];
            for ($i = 0; $i < $purchase->quantity; $i++) {
            $ticket = Ticket::create([
                'event_id' => $purchase->event_id,
                'user_id' => $purchase->user_id,
                'ticket_number' => Ticket::generateTicketNumber(),
                    'verification_code' => Ticket::generateVerificationCode(),
                'purchase_date' => now(),
                    'status' => 'valid',
                    'qr_code' => 'generating...' // Placeholder
            ]);

                // Generate and update QR code
            $ticket->qr_code = $ticket->generateQrCode();
            $ticket->save();
                
                $tickets[] = $ticket;
            }

            // Update purchase record with first ticket ID (for backwards compatibility)
            $firstTicket = $tickets[0] ?? null;

            // Update purchase record
            $purchase->update([
                'ticket_id' => $firstTicket ? $firstTicket->id : null,
                'payment_status' => 'completed',
                'stripe_payment_method_id' => $paymentIntent['payment_method'] ?? null,
            ]);

            // Update event attendee count
            $purchase->event->increment('current_attendees');

            DB::commit();

            \Log::info('Payment completed successfully', [
                'transaction_id' => $purchase->transaction_id,
                'ticket_id' => $firstTicket ? $firstTicket->id : null,
                'tickets_created' => count($tickets)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to complete payment', [
                'transaction_id' => $purchase->transaction_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'purchase_id' => $purchase->id,
                'event_id' => $purchase->event_id,
                'user_id' => $purchase->user_id
            ]);
            
            // Re-throw the exception so the calling method can handle it
            throw $e;
        }
    }

    /**
     * Cancel pending payment
     */
    public function cancelPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:ticket_purchases,transaction_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $purchase = TicketPurchase::where('transaction_id', $request->transaction_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($purchase->payment_status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Can only cancel pending payments'
            ], 400);
        }

        try {
            // Cancel the Stripe PaymentIntent
            if ($purchase->stripe_payment_intent_id) {
                $paymentIntent = PaymentIntent::retrieve($purchase->stripe_payment_intent_id);
                if ($paymentIntent->status === 'requires_payment_method' || $paymentIntent->status === 'requires_confirmation') {
                    $paymentIntent->cancel();
                }
            }

            $purchase->update(['payment_status' => 'cancelled']);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment cancelled successfully'
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Failed to cancel Stripe PaymentIntent', [
                'payment_intent_id' => $purchase->stripe_payment_intent_id,
                'error' => $e->getMessage()
            ]);
            
            // Still mark as cancelled locally
        $purchase->update(['payment_status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment cancelled successfully'
        ]);
        }
    }

    /**
     * Get user's payment history
     */
    public function paymentHistory(Request $request): JsonResponse
    {
        $payments = TicketPurchase::with(['event', 'ticket'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    /**
     * Admin: Manually confirm payment (for testing/admin purposes)
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:ticket_purchases,transaction_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $purchase = TicketPurchase::where('transaction_id', $request->transaction_id)->first();

        if (!$purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($purchase->payment_status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment has already been processed'
            ], 400);
        }

        try {
            // For admin manual confirmation, we'll simulate a successful payment
            $this->completePayment($purchase, ['payment_method' => 'manual_admin_confirmation']);

            $freshPurchase = $purchase->fresh();
            $ticket = $freshPurchase->ticket;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Payment confirmed manually',
                'data' => [
                    'ticket' => $ticket ? $ticket->load('event') : null,
                    'purchase' => $freshPurchase
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Payment confirmation failed', [
                'transaction_id' => $request->transaction_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to confirm payment: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Public payment confirmation for debugging (REMOVE IN PRODUCTION)
     */
    public function confirmPaymentPublic(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:ticket_purchases,transaction_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $purchase = TicketPurchase::where('transaction_id', $request->transaction_id)->first();

        if (!$purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($purchase->payment_status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment has already been processed'
            ], 400);
        }

        try {
            // For public confirmation, we'll simulate a successful payment
            $this->completePayment($purchase, ['payment_method' => 'public_confirmation']);

            $freshPurchase = $purchase->fresh();
            $ticket = $freshPurchase->ticket;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Payment confirmed publicly (DEBUG MODE)',
                'data' => [
                    'ticket' => $ticket ? $ticket->load('event') : null,
                    'purchase' => $freshPurchase
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Public payment confirmation failed', [
                'transaction_id' => $request->transaction_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to confirm payment: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}