<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Add user_id if not exists
            if (!Schema::hasColumn('tickets', 'user_id')) {
                $table->foreignId('user_id')->after('show_id')->constrained('users')->onDelete('cascade');
            }

            // Remove customer_id
            if (Schema::hasColumn('tickets', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Add customer_id back
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained('customers')->onDelete('cascade');

            // Remove user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
