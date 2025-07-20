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
        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['show_id', 'ticket_mode', 'status']);
            $table->index(['booking_id', 'status']);
        });

        Schema::table('seat_reservations', function (Blueprint $table) {
            $table->index(['show_id', 'status', 'reservation_type']);
            $table->index(['expires_at']);
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->index(['venue_id', 'status', 'is_active']);
            $table->index(['seat_category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['show_id', 'ticket_mode', 'status']);
            $table->dropIndex(['booking_id', 'status']);
        });

        Schema::table('seat_reservations', function (Blueprint $table) {
            $table->dropIndex(['show_id', 'status', 'reservation_type']);
            $table->dropIndex(['expires_at']);
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->dropIndex(['venue_id', 'status', 'is_active']);
            $table->dropIndex(['seat_category_id', 'status']);
        });
    }
};
