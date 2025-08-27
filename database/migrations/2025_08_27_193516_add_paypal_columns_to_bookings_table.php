<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add missing PayPal columns
            $table->string('paypal_payer_id')->nullable()->after('payment_reference');
            $table->string('paypal_capture_id')->nullable()->after('paypal_payer_id');
            $table->decimal('paypal_fee', 8, 2)->nullable()->after('paypal_capture_id');
            $table->json('paypal_transaction_data')->nullable()->after('paypal_fee');
            $table->timestamp('paypal_processed_at')->nullable()->after('paypal_transaction_data');
            
            // Add other missing columns that might be referenced
            // $table->string('card_last_four', 4)->nullable()->after('paypal_processed_at');
            // $table->string('card_type')->nullable()->after('card_last_four');
            // $table->timestamp('confirmed_at')->nullable()->after('card_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'paypal_payer_id',
                'paypal_capture_id', 
                'paypal_fee',
                'paypal_transaction_data',
                'paypal_processed_at'
                // 'card_last_four',
                // 'card_type',
                // 'confirmed_at'
            ]);
        });
    }
};
