<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TicketPurchase Model
 * Handles ticket purchase transactions and payment records
 */
class TicketPurchase extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'ticket_id',
        'amount_paid',
        'payment_method',
        'transaction_id',
        'payment_status',
        'bakong_qr_data',
        'bakong_payment_reference',
        'payment_expires_at'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'bakong_qr_data' => 'array',
        'payment_expires_at' => 'datetime',
    ];

    /**
     * Get the event for this purchase
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who made this purchase
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket for this purchase
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Generate unique transaction ID
     */
    public static function generateTransactionId(): string
    {
        do {
            $transactionId = 'TXN-' . strtoupper(uniqid());
        } while (self::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
