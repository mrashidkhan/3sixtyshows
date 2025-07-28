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
        // Add performance indexes for bookings
        Schema::table('bookings', function (Blueprint $table) {
            $this->addIndexIfNotExists('bookings', 'bookings_customer_id_status_index', ['customer_id', 'status']);
            $this->addIndexIfNotExists('bookings', 'bookings_show_id_status_index', ['show_id', 'status']);
            $this->addIndexIfNotExists('bookings', 'bookings_status_created_at_index', ['status', 'created_at']);
            $this->addIndexIfNotExists('bookings', 'bookings_payment_status_created_at_index', ['payment_status', 'created_at']);
            $this->addIndexIfNotExists('bookings', 'bookings_expires_at_index', ['expires_at']);
        });

        // Add performance indexes for seats
        Schema::table('seats', function (Blueprint $table) {
            $this->addIndexIfNotExists('seats', 'seats_venue_id_is_active_index', ['venue_id', 'is_active']);
            $this->addIndexIfNotExists('seats', 'seats_seat_category_id_is_active_index', ['seat_category_id', 'is_active']);
        });

        // Add performance indexes for seat_reservations
        Schema::table('seat_reservations', function (Blueprint $table) {
            // These indexes already exist, so we'll skip them
            // $this->addIndexIfNotExists('seat_reservations', 'seat_reservations_show_id_status_index', ['show_id', 'status']);
            // $this->addIndexIfNotExists('seat_reservations', 'seat_reservations_seat_id_show_id_index', ['seat_id', 'show_id']);

            // Add new indexes with correct column names
            $this->addIndexIfNotExists('seat_reservations', 'seat_reservations_reserved_by_status_index', ['reserved_by', 'status']);
            $this->addIndexIfNotExists('seat_reservations', 'seat_reservations_booking_id_status_index', ['booking_id', 'status']);
            $this->addIndexIfNotExists('seat_reservations', 'seat_reservations_reserved_until_status_index', ['reserved_until', 'status']);
        });

        // Add performance indexes for tickets
        Schema::table('tickets', function (Blueprint $table) {
            $this->addIndexIfNotExists('tickets', 'tickets_show_id_status_index', ['show_id', 'status']);
            $this->addIndexIfNotExists('tickets', 'tickets_customer_id_status_index', ['customer_id', 'status']);
            $this->addIndexIfNotExists('tickets', 'tickets_booking_id_status_index', ['booking_id', 'status']);
            $this->addIndexIfNotExists('tickets', 'tickets_ticket_type_id_status_index', ['ticket_type_id', 'status']);
        });

        // Add performance indexes for shows
        Schema::table('shows', function (Blueprint $table) {
            $this->addIndexIfNotExists('shows', 'shows_is_active_status_index', ['is_active', 'status']);
            $this->addIndexIfNotExists('shows', 'shows_venue_id_is_active_index', ['venue_id', 'is_active']);
            $this->addIndexIfNotExists('shows', 'shows_start_date_is_active_index', ['start_date', 'is_active']);
            $this->addIndexIfNotExists('shows', 'shows_category_id_is_active_index', ['category_id', 'is_active']);
        });

        // Add performance indexes for ticket_types
        Schema::table('ticket_types', function (Blueprint $table) {
            $this->addIndexIfNotExists('ticket_types', 'ticket_types_show_id_is_active_index', ['show_id', 'is_active']);
        });

        // Add performance indexes for booking_items
        Schema::table('booking_items', function (Blueprint $table) {
            $this->addIndexIfNotExists('booking_items', 'booking_items_booking_id_index', ['booking_id']);
            $this->addIndexIfNotExists('booking_items', 'booking_items_ticket_type_id_index', ['ticket_type_id']);
            $this->addIndexIfNotExists('booking_items', 'booking_items_seat_id_index', ['seat_id']);
        });
    }

    /**
     * Add index if it doesn't exist
     */
    private function addIndexIfNotExists($table, $indexName, $columns)
    {
        $connection = Schema::getConnection();

        // Check if index exists using raw SQL to avoid Doctrine DBAL enum issues
        $existingIndexes = $connection->select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);

        if (empty($existingIndexes)) {
            $columnList = implode(',', array_map(function($col) {
                return "`{$col}`";
            }, $columns));

            $connection->statement("CREATE INDEX `{$indexName}` ON `{$table}` ({$columnList})");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'status']);
            $table->dropIndex(['show_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['payment_status', 'created_at']);
            $table->dropIndex(['expires_at']);
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->dropIndex(['venue_id', 'is_active']);
            $table->dropIndex(['seat_category_id', 'is_active']);
        });

        Schema::table('seat_reservations', function (Blueprint $table) {
            $table->dropIndex(['show_id', 'status']);
            $table->dropIndex(['seat_id', 'show_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['booking_id', 'status']);
            $table->dropIndex(['reserved_until', 'status']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['show_id', 'status']);
            $table->dropIndex(['customer_id', 'status']);
            $table->dropIndex(['booking_id', 'status']);
            $table->dropIndex(['ticket_type_id', 'status']);
        });

        Schema::table('shows', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'status']);
            $table->dropIndex(['venue_id', 'is_active']);
            $table->dropIndex(['start_date', 'is_active']);
            $table->dropIndex(['category_id', 'is_active']);
        });

        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropIndex(['show_id', 'is_active']);
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropIndex(['booking_id']);
            $table->dropIndex(['ticket_type_id']);
            $table->dropIndex(['seat_id']);
        });
    }
};
