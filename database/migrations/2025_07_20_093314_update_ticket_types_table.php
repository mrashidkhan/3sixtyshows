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
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->enum('type', ['assigned_seat', 'general_admission', 'standing'])->default('general_admission')->after('description');
            $table->json('seat_category_ids')->nullable()->after('type'); // For assigned seating
            $table->boolean('allows_seat_selection')->default(false)->after('seat_category_ids');
            $table->integer('available_quantity')->nullable()->after('capacity'); // Separate from capacity
            $table->integer('sold_quantity')->default(0)->after('available_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn(['type', 'seat_category_ids', 'allows_seat_selection', 'available_quantity', 'sold_quantity']);
        });
    }
};
