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
        Schema::table('messages', function (Blueprint $table) {
             $table->foreignId('conversation_id')->after('id')->constrained()->cascadeOnDelete();
                  $table->text('body')->after('subject');
      $table->enum('status', ['sent', 'delivered', 'read'])
              ->default('sent')
              ->after('body');
                   $table->dropColumn(['name', 'email', 'message']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
                $table->dropForeign(['conversation_id']);
        $table->dropColumn(['conversation_id', 'body', 'status', 'read_at']);

        
        $table->string('name');
        $table->string('email');
        $table->text('message');
        });
    }
};
