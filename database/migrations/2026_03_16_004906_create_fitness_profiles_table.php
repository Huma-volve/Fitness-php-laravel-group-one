<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->integer('age');
            $table->integer('height_cm');
            $table->decimal('weight_kg', 5, 2);
            $table->enum('fitness_goal', ['weight_loss', 'muscle_gain', 'endurance', 'flexibility', 'general_fitness', 'strength']);
            $table->enum('fitness_level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('workout_location', ['online' , 'in_person_training', 'both']);
            $table->enum('preferred_training_days' , ['1-2' , '3-4' , '5+'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_profiles');
    }
};
