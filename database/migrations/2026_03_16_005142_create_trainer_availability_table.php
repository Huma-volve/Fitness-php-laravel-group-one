<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->dateTime('date');
            $table->time('start_time');
            $table->time('end_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_availabilities');
    }
};
