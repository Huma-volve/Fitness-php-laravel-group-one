<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('trainer_amount', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->string('payout_status')->default('pending');
            $table->timestamp('payout_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_payouts');
    }
};
