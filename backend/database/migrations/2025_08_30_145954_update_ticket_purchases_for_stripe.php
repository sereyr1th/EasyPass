<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            // Remove Bakong-specific columns
            $table->dropColumn(['bakong_qr_data', 'bakong_payment_reference']);
            
            // Add Stripe-specific columns
            $table->string('stripe_payment_intent_id')->nullable()->after('transaction_id');
            $table->string('stripe_payment_method_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_client_secret')->nullable()->after('stripe_payment_method_id');
            $table->json('stripe_metadata')->nullable()->after('stripe_client_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            // Remove Stripe columns
            $table->dropColumn(['stripe_payment_intent_id', 'stripe_payment_method_id', 'stripe_client_secret', 'stripe_metadata']);
            
            // Add back Bakong columns
            $table->json('bakong_qr_data')->nullable();
            $table->string('bakong_payment_reference')->nullable();
        });
    }
};
