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

/**
 * Ticket Controller
 * Handles ticket purchasing, QR code generation, validation, and user ticket history
 */
class TicketController extends Controller
{
    /**
     * Get user's tickets
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = Ticket::with(['event', 'purchase'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $tickets
        ]);
    }

    /**
     * Purchase a ticket for an event
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'payment_method' => 'string|in:online,cash,card'
        ]);

        if ($validator->fails()) {
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

            // Create ticket
            $ticket = Ticket::create([
                'event_id' => $event->id,
                'user_id' => $request->user()->id,
                'ticket_number' => Ticket::generateTicketNumber(),
                'purchase_date' => now(),
                'status' => 'valid'
            ]);

            // Generate QR code
            $ticket->qr_code = $ticket->generateQrCode();
            $ticket->save();

            // Create purchase record
            $purchase = TicketPurchase::create([
                'event_id' => $event->id,
                'user_id' => $request->user()->id,
                'ticket_id' => $ticket->id,
                'amount_paid' => $event->price,
                'payment_method' => $request->get('payment_method', 'online'),
                'transaction_id' => TicketPurchase::generateTransactionId(),
                'payment_status' => 'completed'
            ]);

            // Update event attendee count
            $event->increment('current_attendees');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket purchased successfully',
                'data' => [
                    'ticket' => $ticket->load(['event', 'purchase']),
                    'purchase' => $purchase
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process ticket purchase'
            ], 500);
        }
    }

    /**
     * Display the specified ticket with QR code
     */
    public function show(string $id): JsonResponse
    {
        $ticket = Ticket::with(['event', 'user', 'purchase'])
            ->where('id', $id)
            ->where('user_id', request()->user()->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found or unauthorized access'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $ticket
        ]);
    }

    /**
     * Validate a ticket using QR code or ticket number
     */
    public function validate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ticket_number' => 'required|string|exists:tickets,ticket_number',
            'mark_as_used' => 'boolean' // Optional parameter to control if ticket should be marked as used
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid ticket number',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = Ticket::with(['event', 'user'])
            ->where('ticket_number', $request->ticket_number)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found'
            ], 404);
        }

        // Check ticket status
        if ($ticket->status === 'used') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket has already been used',
                'data' => [
                    'ticket' => $ticket,
                    'used_at' => $ticket->used_at
                ]
            ], 400);
        }

        if ($ticket->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket has been cancelled'
            ], 400);
        }

        // Check if event is today or in the past
        if ($ticket->event->event_date->isPast() && !$ticket->event->event_date->isToday()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event has already passed'
            ], 400);
        }

        // Only mark ticket as used if explicitly requested (for actual entry)
        $validatedAt = now();
        if ($request->boolean('mark_as_used', true)) { // Default to true for backward compatibility
            $ticket->update([
                'status' => 'used',
                'used_at' => $validatedAt
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket validated successfully',
            'data' => [
                'ticket' => $ticket,
                'event' => $ticket->event,
                'user' => $ticket->user,
                'validated_at' => $validatedAt,
                'was_marked_used' => $request->boolean('mark_as_used', true)
            ]
        ]);
    }

    /**
     * Cancel a ticket (if allowed)
     */
    public function cancel(string $id): JsonResponse
    {
        $ticket = Ticket::with('event')
            ->where('id', $id)
            ->where('user_id', request()->user()->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found or unauthorized access'
            ], 404);
        }

        // Check if event allows refunds
        if (!$ticket->event->refundable) {
            return response()->json([
                'status' => 'error',
                'message' => 'This event does not allow ticket refunds'
            ], 400);
        }

        // Check if ticket can be cancelled (e.g., 24 hours before event)
        $eventDate = $ticket->event->event_date;
        $now = now();
        
        // Check if event is in the past
        if ($eventDate->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot cancel tickets for past events'
            ], 400);
        }
        
        // Check if less than 24 hours until event
        $hoursUntilEvent = $now->diffInHours($eventDate, false);
        if ($hoursUntilEvent < 24) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tickets cannot be cancelled less than 24 hours before the event'
            ], 400);
        }

        if ($ticket->status !== 'valid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only valid tickets can be cancelled'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $purchase = $ticket->purchase;
            $refundProcessed = false;
            $refundMessage = '';

            // Process Stripe refund if payment was made via Stripe
            if ($purchase && $purchase->payment_method === 'stripe' && $purchase->stripe_payment_intent_id) {
                try {
                    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                    
                    $refund = \Stripe\Refund::create([
                        'payment_intent' => $purchase->stripe_payment_intent_id,
                        'reason' => 'requested_by_customer',
                        'metadata' => [
                            'ticket_id' => $ticket->id,
                            'event_id' => $ticket->event_id,
                            'user_id' => $ticket->user_id,
                        ]
                    ]);

                    if ($refund->status === 'succeeded') {
                        $refundProcessed = true;
                        $refundMessage = 'Refund of $' . number_format($purchase->amount_paid, 2) . ' has been processed and will appear in your account within 5-10 business days.';
                    }

                    \Log::info('Stripe refund processed', [
                        'refund_id' => $refund->id,
                        'ticket_id' => $ticket->id,
                        'amount' => $refund->amount / 100
                    ]);

                } catch (\Stripe\Exception\ApiErrorException $e) {
                    \Log::error('Stripe refund failed', [
                        'error' => $e->getMessage(),
                        'ticket_id' => $ticket->id,
                        'payment_intent_id' => $purchase->stripe_payment_intent_id
                    ]);
                    // Continue with cancellation even if refund fails - admin can process manually
                    $refundMessage = 'Ticket cancelled. Refund processing failed - please contact support for manual refund.';
                }
            }

            // Update ticket status
            $ticket->update(['status' => 'cancelled']);

            // Update purchase status
            if ($purchase) {
                $purchase->update(['payment_status' => $refundProcessed ? 'refunded' : 'cancelled']);
            }

            // Decrease event attendee count
            $ticket->event->decrement('current_attendees');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket cancelled successfully',
                'refund_info' => $refundMessage ?: 'Ticket cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel ticket'
            ], 500);
        }
    }

    /**
     * Download ticket as PDF (placeholder for future implementation)
     */
    public function download(string $id): JsonResponse
    {
        $ticket = Ticket::with(['event', 'user'])
            ->where('id', $id)
            ->where('user_id', request()->user()->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found or unauthorized access'
            ], 404);
        }

        // This would generate a PDF ticket in a real implementation
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket download ready',
            'data' => [
                'ticket' => $ticket,
                'download_url' => url("/api/tickets/{$id}/pdf") // Placeholder
            ]
        ]);
    }
}
