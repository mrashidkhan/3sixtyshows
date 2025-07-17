<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->text('performers')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->json('performers')->nullable()->change();
        });
    }
};
