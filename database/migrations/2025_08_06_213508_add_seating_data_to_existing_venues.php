<?php
// database/migrations/xxxx_add_seating_data_to_existing_venues.php

use Illuminate\Database\Migrations\Migration;
use App\Models\Venue;
use App\Models\SeatCategory;
use App\Models\Seat;
use App\Models\GeneralAdmissionArea;

return new class extends Migration
{
    public function up()
    {
        // Create seat categories
        $premium = SeatCategory::create([
            'name' => 'Premium',
            'description' => 'Premium seating with best view',
            'color_code' => '#FFD700',
            'price' => 2500.00,
            'is_active' => true,
            'display_order' => 1
        ]);

        $standard = SeatCategory::create([
            'name' => 'Standard',
            'description' => 'Standard seating',
            'color_code' => '#87CEEB',
            'price' => 1500.00,
            'is_active' => true,
            'display_order' => 2
        ]);

        // Add seats and GA areas to existing venues
        $venues = Venue::all();

        foreach($venues as $venue) {
            // Add seats (simplified version)
            for($row = 1; $row <= 5; $row++) {
                for($seat = 1; $seat <= 10; $seat++) {
                    Seat::create([
                        'venue_id' => $venue->id,
                        'seat_category_id' => $row <= 2 ? $premium->id : $standard->id,
                        'section' => 'A',
                        'row' => (string)$row,
                        'seat_number' => (string)$seat,
                        'coordinates_x' => 50 + ($seat * 25),
                        'coordinates_y' => 50 + ($row * 30),
                        'status' => 'available',
                        'is_active' => true
                    ]);
                }
            }

            // Add GA area
            GeneralAdmissionArea::create([
                'venue_id' => $venue->id,
                'name' => 'Standing Area',
                'description' => 'General admission area',
                'capacity' => 100,
                'default_price' => 600.00,
                'color_code' => '#FF6B6B',
                'is_active' => true
            ]);
        }
    }

    public function down()
    {
        Seat::truncate();
        SeatCategory::truncate();
        GeneralAdmissionArea::truncate();
    }
};
