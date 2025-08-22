<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('card_last_four', 4)->nullable()->after('payment_method');
            $table->string('card_type', 20)->nullable()->after('card_last_four');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['card_last_four', 'card_type']);
        });
    }
};
