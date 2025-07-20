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
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('ticket_type_id');
            $table->unsignedBigInteger('seat_id')->nullable(); // For assigned seating
            $table->unsignedBigInteger('general_admission_area_id')->nullable(); // For GA
            $table->integer('quantity'); // Usually 1 for assigned seats, can be > 1 for GA
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('seat_identifier')->nullable(); // Human readable seat info
            $table->json('item_metadata')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')->onDelete('cascade');
            $table->foreign('seat_id')->references('id')->on('seats')->onDelete('cascade');
            $table->foreign('general_admission_area_id')->references('id')->on('general_admission_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_items');
    }
};
