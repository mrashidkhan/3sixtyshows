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
            $table->unsignedBigInteger('seat_id')->nullable()->change(); // Make nullable
            $table->string('seat_identifier')->nullable()->after('seat_number'); // Human readable seat info
            $table->enum('ticket_mode', ['assigned_seat', 'general_admission'])->default('general_admission')->after('seat_identifier');
            $table->json('ticket_metadata')->nullable()->after('ticket_mode'); // Extra info like section for GA
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
