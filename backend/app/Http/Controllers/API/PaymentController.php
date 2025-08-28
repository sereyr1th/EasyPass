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
use GuzzleHttp\Client;

/**
 * Payment Controller
 * Handles Bakong payment processing and QR code generation for ticket purchases
 */
class PaymentController extends Controller
{
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * Initiate payment for ticket purchase
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        \Log::info('Payment initiation request received', $request->all());
        
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'merchant_name' => 'required|string|max:255',
            'merchant_city' => 'required|string|max:255',
            'bakong_account' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
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

        // Check if user already has a ticket for this event
        $existingTicket = Ticket::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingTicket) {
            return response()->json([
                'status' => 'error',
                'message' => 'You already have a ticket for this event'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create pending ticket purchase record
            $purchase = TicketPurchase::create([
                'event_id' => $event->id,
                'user_id' => $request->user()->id,
                'ticket_id' => null, // Will be updated after payment confirmation
                'amount_paid' => $event->price,
                'payment_method' => 'bakong',
                'transaction_id' => TicketPurchase::generateTransactionId(),
                'payment_status' => 'pending',
                'bakong_qr_data' => json_encode([
                    'merchant_name' => $request->merchant_name,
                    'merchant_city' => $request->merchant_city,
                    'bakong_account' => $request->bakong_account,
                    'phone_number' => $request->phone_number,
                    'amount' => $event->price,
                    'currency' => 'USD',
                    'bill_number' => 'TXN-' . time(),
                    'store_label' => 'EasyPass EMS',
                    'terminal_label' => 'Online',
                ])
            ]);

            // Generate Bakong payment data for frontend
            $paymentData = [
                'transaction_id' => $purchase->transaction_id,
                'amount' => $event->price,
                'currency' => 'USD',
                'merchant_name' => $request->merchant_name,
                'merchant_city' => $request->merchant_city,
                'bakong_account' => $request->bakong_account,
                'phone_number' => $request->phone_number,
                'bill_number' => $purchase->transaction_id,
                'store_label' => 'EasyPass EMS',
                'terminal_label' => 'Online',
                'expiration_time' => now()->addMinutes(15)->timestamp, // 15 minutes to complete payment
            ];

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment initiated successfully',
                'data' => [
                    'purchase_id' => $purchase->id,
                    'payment_data' => $paymentData,
                    'event' => $event,
                    'expires_at' => now()->addMinutes(15)
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment initiation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate payment',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Check payment status and complete ticket purchase
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        \Log::info('Payment status check request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:ticket_purchases,transaction_id',
        ]);

        if ($validator->fails()) {
            \Log::error('Payment status validation failed', $validator->errors()->toArray());
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

        // Check if payment has expired (15 minutes)
        if ($purchase->created_at->addMinutes(15)->isPast() && $purchase->payment_status === 'pending') {
            $purchase->update(['payment_status' => 'expired']);
            return response()->json([
                'status' => 'error',
                'message' => 'Payment has expired',
                'data' => ['payment_status' => 'expired']
            ], 400);
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
     * Confirm payment and complete ticket purchase
     * This would typically be called by a webhook or manual confirmation
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:ticket_purchases,transaction_id',
            'payment_reference' => 'string|max:255', // Optional Bakong payment reference
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
                'message' => 'Payment has already been processed or expired'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create ticket
            $ticket = Ticket::create([
                'event_id' => $purchase->event_id,
                'user_id' => $purchase->user_id,
                'ticket_number' => Ticket::generateTicketNumber(),
                'purchase_date' => now(),
                'status' => 'valid'
            ]);

            // Generate QR code
            $ticket->qr_code = $ticket->generateQrCode();
            $ticket->save();

            // Update purchase record
            $purchase->update([
                'ticket_id' => $ticket->id,
                'payment_status' => 'completed',
                'bakong_payment_reference' => $request->get('payment_reference')
            ]);

            // Update event attendee count
            $purchase->event->increment('current_attendees');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment confirmed and ticket generated successfully',
                'data' => [
                    'ticket' => $ticket->load(['event', 'purchase']),
                    'purchase' => $purchase
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to confirm payment',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
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

        $purchase->update(['payment_status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment cancelled successfully'
        ]);
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
}
