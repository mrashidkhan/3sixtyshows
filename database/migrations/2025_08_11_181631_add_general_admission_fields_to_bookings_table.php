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
            $table->json('ticket_breakdown')->nullable()->after('booking_date');
            $table->decimal('service_fee', 8, 2)->default(0)->after('total_price');
            $table->decimal('processing_fee', 8, 2)->default(0)->after('service_fee');
            $table->decimal('grand_total', 8, 2)->default(0)->after('processing_fee');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['ticket_breakdown', 'service_fee', 'processing_fee', 'grand_total']);
        });
    }
};
