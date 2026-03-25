<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'confirmed', 'canceled'])
                ->default('draft')
                ->after('trainer_package_id');

            $table->timestamp('expires_at')->nullable()->after('cancellation_deadline');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['paypal', 'stripe'])
                ->nullable()
                ->after('amount');

            $table->string('gateway_reference')->nullable()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['gateway_reference']);
            $table->dropColumn('payment_method');
            $table->enum('payment_method', ['credit_card', 'debit_card', 'paypal', 'bank_transfer', 'cash'])
                ->nullable()
                ->after('amount');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['status', 'expires_at']);
            $table->enum('status', ['pending', 'confirmed', 'canceled'])
                ->default('pending')
                ->after('trainer_package_id');
        });
    }
};
