<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->string('certificate_name');
            $table->string('organization');
            $table->integer('year');
            $table->string('path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_certifications');
    }
};
