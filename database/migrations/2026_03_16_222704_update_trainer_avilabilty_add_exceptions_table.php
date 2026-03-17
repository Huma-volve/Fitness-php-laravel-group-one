<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Modify trainer_availability ───────────────────────────────────
        Schema::table('trainer_availabilities', function (Blueprint $table) {
            // Drop the old date-specific column
            $table->dropColumn('date');

            // Add day_of_week enum replacing it
            $table->enum('day_of_week', [
                'monday', 'tuesday', 'wednesday',
                'thursday', 'friday', 'saturday', 'sunday',
            ])->after('trainer_id');

            // Add is_active flag
            $table->boolean('is_active')->default(true)->after('end_time');

            // One schedule row per day per trainer
            $table->unique(['trainer_id', 'day_of_week']);
        });

        // ── Step 2: Create trainer_availability_exceptions ────────────────────────
        // Handles one-off overrides (day off or special hours on a specific date)
        Schema::create('trainer_availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(false); // false = day off, true = special hours
            $table->time('start_time')->nullable();          // null means full day off
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();            // e.g. "Holiday", "Travel"

            $table->unique(['trainer_id', 'date']);
        });
    }

    public function down(): void
    {
        // Drop exceptions table
        Schema::dropIfExists('trainer_availability_exceptions');

        // Revert trainer_availability back to original structure
        Schema::table('trainer_availabilities', function (Blueprint $table) {
            $table->dropColumn(['day_of_week', 'is_active']);
            $table->dateTime('date')->after('trainer_id');
        });
    }
};
