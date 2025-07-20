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
        Schema::create('show_ticket_quotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('show_id');
            $table->unsignedBigInteger('ticket_type_id');
            $table->unsignedBigInteger('area_id')->nullable(); // Can be seat_category_id or general_admission_area_id
            $table->enum('area_type', ['seat_category', 'general_admission'])->nullable();
            $table->integer('total_quota'); // Total tickets available for this combination
            $table->integer('sold_count')->default(0);
            $table->integer('reserved_count')->default(0); // Temporarily held
            $table->integer('available_count')->virtualAs('total_quota - sold_count - reserved_count');
            $table->decimal('price_override', 10, 2)->nullable(); // Override ticket type price
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('show_id')->references('id')->on('shows')->onDelete('cascade');
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')->onDelete('cascade');

            $table->unique(['show_id', 'ticket_type_id', 'area_id', 'area_type'], 'show_ticket_quota_unique');
            $table->index(['show_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('show_ticket_quotas');
    }
};
