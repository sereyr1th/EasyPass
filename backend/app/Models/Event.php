<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Event Model
 * Handles event management functionality for the EasyPass system
 */
class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'location',
        'event_date',
        'registration_deadline',
        'price',
        'max_attendees',
        'current_attendees',
        'image_url',
        'status',
        'created_by'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * Get the user who created this event
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all tickets for this event
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get all ticket purchases for this event
     */
    public function ticketPurchases(): HasMany
    {
        return $this->hasMany(TicketPurchase::class);
    }

    /**
     * Check if event is available for registration
     */
    public function isAvailable(): bool
    {
        return $this->status === 'active' && 
               $this->current_attendees < $this->max_attendees &&
               $this->registration_deadline > now();
    }
}
