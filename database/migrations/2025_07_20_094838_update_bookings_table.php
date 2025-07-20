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
            $table->enum('booking_type', ['assigned_seats', 'general_admission', 'mixed'])->default('general_admission')->after('number_of_tickets');
            $table->json('seat_details')->nullable()->after('booking_type'); // Store seat information
            $table->json('booking_metadata')->nullable()->after('seat_details'); // Additional booking info
            $table->timestamp('expires_at')->nullable()->after('booking_date'); // For pending bookings
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
            $table->dropColumn(['booking_type', 'seat_details', 'booking_metadata', 'expires_at']);
        });
    }
};
