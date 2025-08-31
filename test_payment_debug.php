<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TicketPurchase;
use App\Models\Event;
use App\Models\User;
use App\Models\Ticket;

echo "=== Payment Confirmation Debug Test ===\n";

try {
    // Find a pending purchase or create a test scenario
    $purchase = TicketPurchase::where('payment_status', 'pending')->first();
    
    if (!$purchase) {
        echo "No pending purchase found. Creating test data...\n";
        
        // Create test user if needed
        $user = User::first();
        if (!$user) {
            echo "No users found. Please ensure you have users in the database.\n";
            exit(1);
        }
        
        // Create test event if needed
        $event = Event::first();
        if (!$event) {
            echo "No events found. Please ensure you have events in the database.\n";
            exit(1);
        }
        
        // Create test purchase
        $purchase = TicketPurchase::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'ticket_id' => null,
            'amount_paid' => '10.00',
            'quantity' => 1,
            'payment_method' => 'stripe',
            'transaction_id' => 'TEST-' . uniqid(),
            'payment_status' => 'pending',
            'stripe_payment_intent_id' => 'pi_test_' . uniqid(),
            'stripe_client_secret' => 'pi_test_secret_' . uniqid(),
            'stripe_metadata' => [],
            'payment_expires_at' => now()->addMinutes(30)
        ]);
        
        echo "Created test purchase: {$purchase->transaction_id}\n";
    } else {
        echo "Using existing purchase: {$purchase->transaction_id}\n";
    }
    
    echo "Step 1: Testing basic QR code generation...\n";
    
    // Test QR code generation first
    $qrData = json_encode([
        'ticket_id' => 999,
        'ticket_number' => 'EP-TEST123',
        'event_id' => $purchase->event_id,
        'user_id' => $purchase->user_id,
        'verification_code' => 'VER-TEST123',
        'generated_at' => now()->toISOString(),
        'status' => 'valid'
    ]);
    
    $qrCode = new Endroid\QrCode\QrCode($qrData);
    $writer = new Endroid\QrCode\Writer\PngWriter();
    $qrResult = $writer->write($qrCode);
    $qrBase64 = base64_encode($qrResult->getString());
    
    echo "✓ QR code generation successful (" . strlen($qrBase64) . " chars)\n";
    
    echo "Step 2: Testing ticket creation...\n";
    
    // Create a test ticket
    $ticket = new Ticket([
        'event_id' => $purchase->event_id,
        'user_id' => $purchase->user_id,
        'ticket_number' => Ticket::generateTicketNumber(),
        'verification_code' => Ticket::generateVerificationCode(),
        'purchase_date' => now(),
        'status' => 'valid',
        'qr_code' => 'generating...'
    ]);
    
    echo "✓ Ticket model created\n";
    
    echo "Step 3: Testing QR code generation via Ticket model...\n";
    
    // This is where the error might occur
    $ticket->id = 999; // Simulate having an ID
    $generatedQr = $ticket->generateQrCode();
    
    echo "✓ QR code generated via Ticket model (" . strlen($generatedQr) . " chars)\n";
    
    echo "Step 4: Testing full ticket save...\n";
    
    // Don't actually save to avoid conflicts, just test the QR generation part
    $ticket->qr_code = $generatedQr;
    
    echo "✓ Full ticket process completed successfully\n";
    
    echo "\n=== All tests passed! The QR generation is working correctly. ===\n";
    echo "The error might be occurring in a different context or with different data.\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR FOUND!\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Class: " . get_class($e) . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
