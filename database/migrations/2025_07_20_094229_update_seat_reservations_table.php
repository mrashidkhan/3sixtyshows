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
        Schema::table('seat_reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('ticket_type_id')->nullable()->after('ticket_id');
            $table->enum('reservation_type', ['permanent', 'temporary', 'held'])->default('permanent')->after('status');
            $table->timestamp('expires_at')->nullable()->after('reserved_until');

            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seat_reservations', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropColumn(['ticket_type_id', 'reservation_type', 'expires_at']);
        });
    }
};
