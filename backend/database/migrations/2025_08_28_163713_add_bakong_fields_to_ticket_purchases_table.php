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
            $table->json('bakong_qr_data')->nullable()->after('payment_status');
            $table->string('bakong_payment_reference')->nullable()->after('bakong_qr_data');
            $table->timestamp('payment_expires_at')->nullable()->after('bakong_payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            $table->dropColumn(['bakong_qr_data', 'bakong_payment_reference', 'payment_expires_at']);
        });
    }
};
