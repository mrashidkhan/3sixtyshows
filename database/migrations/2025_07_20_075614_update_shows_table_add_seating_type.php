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
        Schema::table('shows', function (Blueprint $table) {
            $table->enum('seating_type', ['assigned', 'general_admission', 'mixed'])->default('general_admission')->after('venue_id');
            $table->boolean('requires_seat_selection')->default(false)->after('seating_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropColumn(['seating_type', 'requires_seat_selection']);
        });
    }
};
