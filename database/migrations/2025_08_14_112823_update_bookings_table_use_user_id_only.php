<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Remove customer_id (user_id already exists)
            if (Schema::hasColumn('bookings', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add customer_id back
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained('customers')->onDelete('cascade');
        });
    }
};
