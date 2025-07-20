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
        Schema::create('general_admission_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_id');
            $table->string('name'); // e.g., "General Admission", "Standing Room", "VIP Area"
            $table->text('description')->nullable();
            $table->integer('capacity'); // Maximum people in this area
            $table->decimal('default_price', 10, 2)->nullable();
            $table->string('color_code', 7)->nullable(); // For visual representation
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->index(['venue_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_admission_areas');
    }
};
