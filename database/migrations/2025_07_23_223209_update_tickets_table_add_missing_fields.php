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
            if (!Schema::hasColumn('tickets', 'seat_identifier')) {
                $table->string('seat_identifier')->nullable()->after('seat_number');
            }
            if (!Schema::hasColumn('tickets', 'ticket_mode')) {
                $table->string('ticket_mode')->default('assigned_seat')->after('qr_code');
            }
            if (!Schema::hasColumn('tickets', 'ticket_metadata')) {
                $table->json('ticket_metadata')->nullable()->after('ticket_mode');
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
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['seat_identifier', 'ticket_mode', 'ticket_metadata']);
        });
    }
};
