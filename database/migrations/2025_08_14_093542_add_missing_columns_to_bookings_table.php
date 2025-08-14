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
            // Add missing columns
            if (!Schema::hasColumn('bookings', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('bookings', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->after('show_id');
            }

            if (!Schema::hasColumn('bookings', 'service_fee')) {
                $table->decimal('service_fee', 10, 2)->after('total_amount');
            }

            if (!Schema::hasColumn('bookings', 'processing_fee')) {
                $table->decimal('processing_fee', 10, 2)->after('service_fee');
            }

            if (!Schema::hasColumn('bookings', 'grand_total')) {
                $table->decimal('grand_total', 10, 2)->after('processing_fee');
            }

            if (!Schema::hasColumn('bookings', 'ticket_breakdown')) {
                $table->json('ticket_breakdown')->nullable()->after('number_of_tickets');
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
            //
        });
    }
};
