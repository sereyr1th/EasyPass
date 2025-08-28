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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('location');
            $table->dateTime('event_date');
            $table->dateTime('registration_deadline');
            $table->decimal('price', 10, 2);
            $table->integer('max_attendees');
            $table->integer('current_attendees')->default(0);
            $table->string('image_url')->nullable();
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
