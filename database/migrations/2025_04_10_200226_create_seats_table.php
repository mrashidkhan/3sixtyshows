// Migration for Seats table
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->string('section'); // e.g., Orchestra, Mezzanine, Balcony
            $table->string('row'); // e.g., A, B, C or 1, 2, 3
            $table->string('seat_number');
            $table->enum('status', ['available', 'reserved', 'sold', 'blocked'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Create a unique constraint on the combination of venue, section, row, and seat
            $table->unique(['venue_id', 'section', 'row', 'seat_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('seats');
    }
};
