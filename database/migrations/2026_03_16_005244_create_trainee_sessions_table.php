<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('session_start')->nullable();
            $table->timestamp('session_end')->nullable();
            $table->enum('session_status', ['scheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
