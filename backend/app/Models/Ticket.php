<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Ticket Model
 * Handles ticket generation and QR code functionality
 */
class Ticket extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'ticket_number',
        'qr_code',
        'verification_code',
        'status',
        'purchase_date',
        'used_at'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Get the event this ticket belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who owns this ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchase record for this ticket
     */
    public function purchase(): HasOne
    {
        return $this->hasOne(TicketPurchase::class);
    }

    /**
     * Generate QR code for the ticket
     */
    public function generateQrCode(): string
    {
        $qrData = json_encode([
            'ticket_id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'event_id' => $this->event_id,
            'user_id' => $this->user_id,
            'verification_code' => $this->verification_code,
            'generated_at' => now()->toISOString(),
            'status' => $this->status
        ]);
        
        // Try SVG writer first (doesn't require GD)
        try {
            $qrCode = new QrCode($qrData);
            $writer = new \Endroid\QrCode\Writer\SvgWriter();
            
            $result = $writer->write($qrCode);
            $svgContent = $result->getString();
            
            // Convert SVG to base64 data URI for compatibility
            return 'data:image/svg+xml;base64,' . base64_encode($svgContent);
            
        } catch (\Exception $svgException) {
            \Log::warning("SVG QR Code generation failed, trying PNG with retries", [
                'ticket_id' => $this->id,
                'svg_error' => $svgException->getMessage()
            ]);
            
            // Fallback to PNG with retry logic
            $maxRetries = 3;
            $retryDelay = 100000; // 100ms in microseconds
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $qrCode = new QrCode($qrData);
                    $writer = new PngWriter();
                    
                    $result = $writer->write($qrCode);
                    return base64_encode($result->getString());
                    
                } catch (\Exception $e) {
                    \Log::error("PNG QR Code generation attempt {$attempt} failed", [
                        'ticket_id' => $this->id,
                        'error' => $e->getMessage(),
                        'memory_usage' => memory_get_usage(true),
                        'memory_peak' => memory_get_peak_usage(true),
                        'gd_info' => function_exists('gd_info') ? gd_info() : 'GD not available'
                    ]);
                    
                    if ($attempt === $maxRetries) {
                        // Last attempt failed, use text-based fallback
                        \Log::error("All QR generation attempts failed, using text fallback", [
                            'ticket_id' => $this->id,
                            'final_error' => $e->getMessage()
                        ]);
                        
                        return $this->generateTextBasedQrFallback();
                    }
                    
                    // Wait before retrying
                    usleep($retryDelay);
                    
                    // Increase delay for next attempt
                    $retryDelay *= 2;
                    
                    // Force garbage collection to free memory
                    if (function_exists('gc_collect_cycles')) {
                        gc_collect_cycles();
                    }
                }
            }
        }
        
        // This should never be reached, but just in case
        return $this->generateTextBasedQrFallback();
    }
    
    /**
     * Generate a text-based fallback when QR generation fails
     */
    private function generateTextBasedQrFallback(): string
    {
        // Create a simple text-based representation that can be displayed
        $fallbackData = [
            'ticket_number' => $this->ticket_number,
            'verification_code' => $this->verification_code,
            'event_id' => $this->event_id,
            'status' => 'QR_GENERATION_FAILED'
        ];
        
        // Return a base64 encoded JSON string that the frontend can handle
        return 'data:text/plain;base64,' . base64_encode(json_encode($fallbackData));
    }

    /**
     * Generate verification code
     */
    public static function generateVerificationCode(): string
    {
        return 'VER-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        do {
            $ticketNumber = 'EP-' . strtoupper(uniqid());
        } while (self::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }
}
