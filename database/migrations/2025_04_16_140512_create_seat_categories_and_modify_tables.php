<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatCategoriesAndModifyTables extends Migration
{
    public function up()
    {
        // Create seat_categories table
        // Schema::create('seat_categories', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->text('description')->nullable();
        //     $table->string('color_code', 7)->nullable(); // For visual representation (e.g., #FF0000)
        //     $table->decimal('price', 10, 2)->default(0);
        //     $table->boolean('is_active')->default(true);
        //     $table->integer('display_order')->default(0);
        //     $table->timestamps();
        // });

        // Modify seats table for more detailed seating
        Schema::table('seats', function (Blueprint $table) {
            // Add if you don't already have these fields
            // $table->foreignId('seat_category_id')->nullable()->constrained()->onDelete('set null');
            // $table->integer('coordinates_x')->nullable(); // For visual seat map
            // $table->integer('coordinates_y')->nullable(); // For visual seat map

            // Drop the existing status column and recreate it with the new enum values
            // $table->dropColumn('status');
            // $table->enum('status', ['available', 'maintenance', 'disabled'])->default('available');
        });

        // Create seat_reservations table
        // Schema::create('seat_reservations', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('show_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('seat_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('ticket_id')->nullable()->constrained()->onDelete('set null');
        //     $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
        //     $table->enum('status', ['temporary', 'reserved', 'booked', 'cancelled'])->default('temporary');
        //     $table->foreignId('reserved_by')->nullable()->constrained('users')->onDelete('set null');
        //     $table->dateTime('reserved_until')->nullable();
        //     $table->text('notes')->nullable();
        //     $table->timestamps();

        //     // Unique constraint to prevent double bookings
        //     $table->unique(['show_id', 'seat_id']);
        // });

        // Update tickets table
        Schema::table('tickets', function (Blueprint $table) {
            // Only add seat_id if it doesn't already exist
            if (!Schema::hasColumn('tickets', 'seat_id')) {
                $table->foreignId('seat_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }

    public function down()
    {
        // Drop the seat_reservations table first due to foreign key constraints
        Schema::dropIfExists('seat_reservations');

        // Remove the foreign key and columns from the seats table
        Schema::table('seats', function (Blueprint $table) {
            $table->dropForeign(['seat_category_id']);
            $table->dropColumn(['seat_category_id', 'coordinates_x', 'coordinates_y']);

            // Restore the original status column
            $table->dropColumn('status');
            $table->enum('status', ['available', 'reserved', 'sold', 'blocked'])->default('available');
        });

        // Drop the seat_categories table
        Schema::dropIfExists('seat_categories');

        // Remove the foreign key from the tickets table
        if (Schema::hasColumn('tickets', 'seat_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['seat_id']);
                $table->dropColumn('seat_id');
            });
        }
    }
}
