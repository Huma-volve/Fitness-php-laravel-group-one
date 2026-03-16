<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->integer('sessions');
            $table->integer('duration_days');
            $table->boolean('progress_tracking')->default(false);
            $table->boolean('nutrition_plan')->default(false);
            $table->boolean('priority_booking')->default(false);
            $table->boolean('full_access')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
