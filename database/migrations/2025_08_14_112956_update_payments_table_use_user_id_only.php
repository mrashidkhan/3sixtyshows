<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add user_id if not exists
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->after('booking_id')->constrained('users')->onDelete('cascade');
            }

            // Remove customer_id
            if (Schema::hasColumn('payments', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained('customers')->onDelete('cascade');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
