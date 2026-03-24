<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires re-defining the full enum to add a new value
        DB::statement("
            ALTER TABLE trainee_sessions
            MODIFY COLUMN session_status
            ENUM('pending_payment','scheduled','completed','cancelled','no_show')
            NOT NULL DEFAULT 'pending_payment'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE trainee_sessions
            MODIFY COLUMN session_status
            ENUM('scheduled','completed','cancelled','no_show')
            NOT NULL DEFAULT 'scheduled'
        ");
    }
};
