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
        Schema::create('ticket_holds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('show_id');
            $table->unsignedBigInteger('ticket_type_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('session_id')->nullable(); // For anonymous users
            $table->unsignedBigInteger('seat_id')->nullable(); // For assigned seating
            $table->unsignedBigInteger('general_admission_area_id')->nullable(); // For GA
            $table->integer('quantity')->default(1);
            $table->enum('hold_type', ['seat_selection', 'quantity_hold']);
            $table->timestamp('expires_at');
            $table->json('hold_data')->nullable(); // Store additional hold information
            $table->timestamps();

            $table->foreign('show_id')->references('id')->on('shows')->onDelete('cascade');
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('seat_id')->references('id')->on('seats')->onDelete('cascade');
            $table->foreign('general_admission_area_id')->references('id')->on('general_admission_areas')->onDelete('cascade');

            $table->index(['expires_at']);
            $table->index(['show_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_holds');
    }
};
