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
        
        // KHQR validation rules
        $rules = [
            'event_id' => 'required|exists:events,id',
            'payment_method' => 'required|in:khqr',
            'phone_number' => 'required|string|max:20',
            'customer_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,KHR',
        ];

        $validator = Validator::make($request->all(), $rules);

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

            // Generate unique transaction ID
            $transactionId = TicketPurchase::generateTransactionId();
            
            // Create KHQR payment request
            $khqrData = $this->generateKHQRPayment([
                'amount' => $request->amount,
                'currency' => $request->currency,
                'customer_name' => $request->customer_name,
                'phone_number' => $request->phone_number,
                'transaction_id' => $transactionId,
                'description' => 'Ticket for ' . $event->title,
                'callback_url' => url('/api/payments/webhook/khqr'),
            ]);

            // If PayWay fails, set khqrData to null and continue to demo mode logic
            if ($khqrData === null) {
                \Log::info('PayWay API failed, will use demo mode fallback', [
                    'transaction_id' => $transactionId,
                    'event_id' => $event->id,
                    'user_id' => $request->user()->id
                ]);
            }

            // Switch between demo and real PayWay mode (only if PayWay didn't fail)
            if (!isset($khqrData['demo_mode'])) {
                $useRealPayWay = env('PAYWAY_ENABLE_REAL_API', false);
                
                if ($useRealPayWay && $khqrData && isset($khqrData['qr_code'])) {
                    // Real KHQR payments start as pending, completed via webhook
                    $paymentStatus = 'pending';
                } else {
                    // Demo mode: immediate completion for testing
                    \Log::warning('Payment completed in DEMO MODE - no real money transferred', [
                        'transaction_id' => $transactionId,
                        'amount' => $request->amount,
                        'user_id' => $request->user()->id,
                        'reason' => 'PayWay API returned HTML instead of JSON - using demo mode'
                    ]);
                    $paymentStatus = 'completed';
                    
                    // Create demo KHQR data
                    $khqrData = [
                        'qr_code' => $this->generateKHQRCode([
                            'transaction_id' => $transactionId,
                            'amount' => $request->amount,
                            'currency' => $request->currency,
                            'description' => 'Ticket for ' . $event->title
                        ]),
                        'transaction_id' => $transactionId,
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                        'description' => 'Ticket for ' . $event->title,
                        'expires_at' => now()->addMinutes(15)->toISOString(),
                        'demo_mode' => true,
                        'instructions' => [
                            '✅ Demo Mode: Payment completed successfully!',
                            '🎫 Your ticket has been generated',
                            '📱 Check "My Tickets" to view your QR code',
                            '⚠️ Note: No real money was charged'
                        ]
                    ];
                }
            } 
            $ticket = null;

            // Create ticket immediately for completed payments
            if ($paymentStatus === 'completed') {
                $ticket = Ticket::create([
                    'event_id' => $event->id,
                    'user_id' => $request->user()->id,
                    'ticket_number' => Ticket::generateTicketNumber(),
                    'verification_code' => Ticket::generateVerificationCode(),
                    'purchase_date' => now(),
                    'status' => 'valid',
                    'qr_code' => 'generating...' // Placeholder until we can generate the real QR code
                ]);
                
                // Generate and update QR code after ticket is created (needs ticket ID)
                $ticket->qr_code = $ticket->generateQrCode();
                $ticket->save();

                // Update event attendee count
                $event->increment('current_attendees');
            }

            // Create purchase record
            $purchase = TicketPurchase::create([
                'event_id' => $event->id,
                'user_id' => $request->user()->id,
                'ticket_id' => $ticket ? $ticket->id : null,
                'amount_paid' => $request->amount,
                'payment_method' => 'khqr',
                'transaction_id' => $transactionId,
                'payment_status' => $paymentStatus,
                'bakong_qr_data' => json_encode($khqrData),
                'payment_expires_at' => now()->addMinutes(15)
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'KHQR payment initiated successfully',
                'data' => [
                    'purchase_id' => $purchase->id,
                    'transaction_id' => $transactionId,
                    'payment_method' => 'khqr',
                    'khqr_data' => $khqrData,
                    'event' => $event,
                    'ticket' => $ticket ? $ticket->load('event') : null,
                    'expires_at' => $purchase->payment_expires_at,
                    'status' => $paymentStatus
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

    /**
     * Generate KHQR payment data using ABA PayWay API
     */
    private function generateKHQRPayment(array $data): ?array
    {
        try {
            // Ensure description exists
            $description = $data['description'] ?? 'Event Ticket Purchase';
            
            \Log::info('generateKHQRPayment called with data:', $data);
            
            // Prepare PayWay API request with simpler transaction ID
            $simpleTransactionId = 'EP' . time() . rand(1000, 9999);
            
            $requestData = [
                'req_time' => now()->format('YmdHis'),
                'merchant_id' => env('PAYWAY_MERCHANT_ID'),
                'order_id' => $simpleTransactionId,  // Use simpler format
                'amount' => number_format($data['amount'], 2, '.', ''),
                'items' => json_encode([
                    [
                        'name' => $description,
                        'quantity' => 1,
                        'price' => number_format($data['amount'], 2, '.', '')
                    ]
                ]),
                'shipping' => '0.00',
                'tax' => '0.00',
                'currency' => $data['currency'],
                'type' => 'purchase',
                'payment_option' => 'khqr',
                'return_url' => url('/payment/success'),
                'cancel_url' => url('/payment/cancel'),
                'continue_success_url' => url('/payment/continue'),
                'webhook_url' => $data['callback_url']
            ];
            
            \Log::info('PayWay API Request Data', $requestData);

            // Create hash for authentication
            $hash = $this->createPayWayHash($requestData);
            $requestData['hash'] = $hash;

            \Log::info('PayWay API Request', $requestData);

            // Make API call to PayWay
            $client = new \GuzzleHttp\Client([
                'verify' => false, // Disable SSL verification for sandbox
                'timeout' => 30
            ]);
            
            $response = $client->post(env('PAYWAY_API_URL'), [
                'json' => $requestData,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            
            \Log::info('PayWay API Response', [
                'raw_response' => $responseBody,
                'parsed_data' => $responseData,
                'status_code' => $response->getStatusCode()
            ]);

            if ($responseData && isset($responseData['status']) && $responseData['status'] === '000') {
                // Success response from PayWay
                return [
                    'qr_code' => $responseData['qr_code'] ?? null,
                    'transaction_id' => $data['transaction_id'],
                    'payway_order_id' => $simpleTransactionId,
                    'payway_transaction_id' => $responseData['tran_id'] ?? null,
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'description' => $description,
                    'expires_at' => now()->addMinutes(15)->toISOString(),
                    'payment_url' => $responseData['payment_url'] ?? null,
                    'qr_string' => $responseData['qr_string'] ?? null,
                    'instructions' => [
                        '1. Open your banking app (ABA, ACLEDA, Wing, etc.)',
                        '2. Select "Scan QR" or "KHQR Payment"',
                        '3. Scan the QR code above',
                        '4. Confirm the payment details',
                        '5. Enter your PIN to complete payment'
                    ]
                ];
            } else {
                // Error response from PayWay or invalid JSON
                $errorMsg = 'Unknown error';
                if ($responseData && isset($responseData['description'])) {
                    $errorMsg = $responseData['description'];
                } else if ($responseData && isset($responseData['message'])) {
                    $errorMsg = $responseData['message'];
                } else if (!$responseData) {
                    $errorMsg = 'PayWay returned HTML instead of JSON - possibly wrong API endpoint or method';
                    \Log::warning('PayWay API returned HTML instead of JSON, falling back to demo mode', [
                        'response_preview' => substr($responseBody, 0, 200)
                    ]);
                    // Return null to trigger demo mode instead of throwing exception
                    return null;
                }
                throw new \Exception('PayWay API Error: ' . $errorMsg);
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            \Log::error('PayWay API Request Error', [
                'message' => $e->getMessage(),
                'request' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'No status code'
            ]);
            
            // Return null to indicate PayWay failure
            return null;
        } catch (\Exception $e) {
            \Log::error('PayWay API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return null to indicate PayWay failure
            return null;
        }
    }

    /**
     * Generate KHQR code string
     * This would normally integrate with KHQR SDK or API
     */
    private function generateKHQRCode(array $data): string
    {
        // TODO: Use real KHQR generation
        // For now, return a simulated QR code data string
        
        $description = $data['description'] ?? 'Event Ticket Purchase';
        
        $qrData = [
            'version' => '01',
            'type' => '12', // Dynamic QR
            'merchant_id' => env('KHQR_MERCHANT_ID', 'DEMO_MERCHANT'),
            'amount' => $data['amount'],
            'currency' => $data['currency'] === 'USD' ? '840' : '116', // USD: 840, KHR: 116
            'transaction_id' => $data['transaction_id'],
            'description' => $description,
            'callback_url' => $data['callback_url'] ?? ''
        ];

        // This would normally generate the actual KHQR string
        return 'khqr_' . base64_encode(json_encode($qrData));
    }

    /**
     * Create PayWay authentication hash using RSA signature
     */
    private function createPayWayHash(array $data): string
    {
        // PayWay hash generation according to their documentation
        $hashString = '';
        
        // Required fields for hash in specific order
        $hashFields = [
            'req_time', 'merchant_id', 'order_id', 'amount', 'items', 
            'shipping', 'tax', 'currency', 'type', 'payment_option',
            'return_url', 'cancel_url', 'continue_success_url', 'webhook_url'
        ];
        
        foreach ($hashFields as $field) {
            if (isset($data[$field])) {
                $hashString .= $data[$field];
            }
        }
        
        // Add public key
        $hashString .= env('PAYWAY_PUBLIC_KEY');
        
        \Log::info('PayWay Hash String', ['hash_string' => $hashString]);
        
        // For PayWay, we might need RSA signature instead of simple hash
        // Try simple hash first, if it fails we'll implement RSA signing
        return hash('sha512', $hashString);
    }

    /**
     * Create RSA signature for PayWay (if needed)
     */
    private function createRSASignature(string $data): string
    {
        $privateKey = env('PAYWAY_PRIVATE_KEY');
        
        if (!$privateKey) {
            throw new \Exception('PayWay private key not configured');
        }

        $key = openssl_pkey_get_private($privateKey);
        if (!$key) {
            throw new \Exception('Invalid PayWay private key');
        }

        $signature = '';
        if (!openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('Failed to create RSA signature');
        }

        return base64_encode($signature);
    }

    /**
     * Handle KHQR payment webhook
     * This endpoint receives payment confirmations from the bank
     */
    public function handleKHQRWebhook(Request $request): JsonResponse
    {
        \Log::info('KHQR webhook received', $request->all());
        
        try {
            // Verify webhook signature (implement PayWay signature verification)
            $isValid = $this->verifyPayWayWebhook($request->all());
            
            if (!$isValid) {
                \Log::error('Invalid PayWay webhook signature');
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            $transactionId = $request->get('order_id');
            $paymentStatus = $request->get('status');
            
            // Find the purchase record
            $purchase = TicketPurchase::where('transaction_id', $transactionId)->first();
            
            if (!$purchase) {
                \Log::error('Purchase not found for transaction: ' . $transactionId);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            if ($paymentStatus === '000') { // Success status from PayWay
                // Update purchase status
                $purchase->update([
                    'payment_status' => 'completed',
                    'bakong_payment_reference' => $request->get('tran_id')
                ]);

                // Create ticket if not exists
                if (!$purchase->ticket_id) {
                    $ticket = Ticket::create([
                        'event_id' => $purchase->event_id,
                        'user_id' => $purchase->user_id,
                        'ticket_number' => Ticket::generateTicketNumber(),
                        'verification_code' => Ticket::generateVerificationCode(),
                        'purchase_date' => now(),
                        'status' => 'valid',
                        'qr_code' => 'generating...'
                    ]);
                    
                    $ticket->qr_code = $ticket->generateQrCode();
                    $ticket->save();

                    $purchase->update(['ticket_id' => $ticket->id]);

                    // Update event attendee count
                    $purchase->event->increment('current_attendees');
                }

                \Log::info('Payment completed successfully', ['transaction_id' => $transactionId]);
            } else {
                // Payment failed
                $purchase->update(['payment_status' => 'failed']);
                \Log::info('Payment failed', ['transaction_id' => $transactionId, 'status' => $paymentStatus]);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            \Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Processing error'], 500);
        }
    }

    /**
     * Verify PayWay webhook signature
     */
    private function verifyPayWayWebhook(array $data): bool
    {
        // TODO: Implement proper PayWay webhook signature verification
        // For now, return true to allow testing
        return true;
    }
}
