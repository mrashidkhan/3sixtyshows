// Migration for Tickets table
// create_tickets_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
            // Add to the tickets table migration
            $table->foreignId('seat_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ticket_number')->unique();
            $table->decimal('price', 10, 2);
            $table->string('status')->default('active'); // active, used, cancelled
            $table->string('seat_number')->nullable();
            $table->dateTime('purchased_date');
            $table->string('qr_code')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
