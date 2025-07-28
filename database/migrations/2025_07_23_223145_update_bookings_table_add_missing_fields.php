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
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add missing fields to match the Booking model
            if (!Schema::hasColumn('bookings', 'booking_data')) {
                $table->json('booking_data')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'expires_at')) {
                $table->timestamp('expires_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'payment_reference')) {
                $table->string('payment_reference')->nullable();
            }
            // Rename total_price to total_amount for consistency
            if (Schema::hasColumn('bookings', 'total_price') && !Schema::hasColumn('bookings', 'total_amount')) {
                $table->renameColumn('total_price', 'total_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['booking_data', 'expires_at', 'confirmed_at', 'payment_reference']);
        });
    }
};
