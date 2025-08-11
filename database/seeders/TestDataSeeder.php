<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Show;
use App\Models\Venue;
use App\Models\Seat;
use App\Models\SeatCategory;
use App\Models\GeneralAdmissionArea;
use App\Models\ShowCategory;
use App\Models\Customer;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create show category first
        $showCategory = ShowCategory::create([
            'name' => 'Bollywood Shows',
            'description' => 'Traditional Bollywood music and dance performances',
            'slug' => 'bollywood-shows',
            'is_active' => true
        ]);

        // Create a venue with all required fields
        $venue = Venue::create([
            'name' => 'Mumbai Arena',
            'slug' => 'mumbai-arena',
            'description' => 'Premier entertainment venue in the heart of Mumbai',
            'address' => '123 Entertainment District, Bandra West',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'country' => 'India',
            'postal_code' => '400050',
            'latitude' => 19.0760,
            'longitude' => 72.8777,
            'contact_email' => 'info@mumbaiarena.com',
            'contact_phone' => '+91 22 1234 5678',
            'website' => 'https://mumbaiarena.com',
            'capacity' => 500
        ]);

        // Create seat categories
        $premiumCategory = SeatCategory::create([
            'name' => 'Premium',
            'description' => 'Premium seating with best view of the stage',
            'color_code' => '#FFD700',
            'base_price' => 2500.00, // Price in INR
            'is_active' => true,
            'display_order' => 1,
            'category_metadata' => [
                'perks' => ['Complimentary snacks', 'Priority entry', 'Best view'],
                'row_range' => '1-2'
            ]
        ]);

        $standardCategory = SeatCategory::create([
            'name' => 'Standard',
            'description' => 'Standard seating with good view',
            'color_code' => '#87CEEB',
            'base_price' => 1500.00, // Price in INR
            'is_active' => true,
            'display_order' => 2,
            'category_metadata' => [
                'perks' => ['Good view', 'Standard amenities'],
                'row_range' => '3-5'
            ]
        ]);

        $economyCategory = SeatCategory::create([
            'name' => 'Economy',
            'description' => 'Budget-friendly seating',
            'color_code' => '#98FB98',
            'base_price' => 800.00, // Price in INR
            'is_active' => true,
            'display_order' => 3,
            'category_metadata' => [
                'perks' => ['Affordable pricing', 'Decent view'],
                'row_range' => '6-10'
            ]
        ]);

        // Create seats - more realistic layout
        // Premium section (Rows 1-2)
        for ($row = 1; $row <= 2; $row++) {
            for ($seat = 1; $seat <= 12; $seat++) {
                Seat::create([
                    'venue_id' => $venue->id,
                    'seat_category_id' => $premiumCategory->id,
                    'section' => 'A',
                    'row' => (string)$row,
                    'seat_number' => (string)$seat,
                    'coordinates_x' => 60 + ($seat * 25), // Better spacing
                    'coordinates_y' => 80 + ($row * 30),
                    'status' => 'available',
                    'is_active' => true,
                    'is_accessible' => $seat == 1 || $seat == 12, // Aisle seats are accessible
                    'seat_metadata' => [
                        'aisle_seat' => $seat == 1 || $seat == 12,
                        'premium_features' => ['Extra legroom', 'Cushioned seats']
                    ]
                ]);
            }
        }

        // Standard section (Rows 3-5)
        for ($row = 3; $row <= 5; $row++) {
            for ($seat = 1; $seat <= 14; $seat++) {
                Seat::create([
                    'venue_id' => $venue->id,
                    'seat_category_id' => $standardCategory->id,
                    'section' => 'B',
                    'row' => (string)$row,
                    'seat_number' => (string)$seat,
                    'coordinates_x' => 50 + ($seat * 22),
                    'coordinates_y' => 80 + ($row * 30),
                    'status' => 'available',
                    'is_active' => true,
                    'is_accessible' => $seat == 1 || $seat == 14,
                    'seat_metadata' => [
                        'aisle_seat' => $seat == 1 || $seat == 14
                    ]
                ]);
            }
        }

        // Economy section (Rows 6-8)
        for ($row = 6; $row <= 8; $row++) {
            for ($seat = 1; $seat <= 16; $seat++) {
                Seat::create([
                    'venue_id' => $venue->id,
                    'seat_category_id' => $economyCategory->id,
                    'section' => 'C',
                    'row' => (string)$row,
                    'seat_number' => (string)$seat,
                    'coordinates_x' => 40 + ($seat * 20),
                    'coordinates_y' => 80 + ($row * 30),
                    'status' => 'available',
                    'is_active' => true,
                    'is_accessible' => $seat == 1 || $seat == 16,
                    'seat_metadata' => [
                        'aisle_seat' => $seat == 1 || $seat == 16
                    ]
                ]);
            }
        }

        // Create general admission areas
        GeneralAdmissionArea::create([
            'venue_id' => $venue->id,
            'name' => 'Standing Area',
            'description' => 'General admission standing area with great energy and close to stage',
            'capacity' => 150,
            'default_price' => 600.00, // Price in INR
            'color_code' => '#FF6B6B',
            'display_order' => 1,
            'is_active' => true
        ]);

        GeneralAdmissionArea::create([
            'venue_id' => $venue->id,
            'name' => 'VIP Lounge',
            'description' => 'Premium general admission with bar access and VIP amenities',
            'capacity' => 50,
            'default_price' => 3500.00, // Price in INR
            'color_code' => '#9B59B6',
            'display_order' => 2,
            'is_active' => true
        ]);

        GeneralAdmissionArea::create([
            'venue_id' => $venue->id,
            'name' => 'Balcony Standing',
            'description' => 'Elevated standing area with bird\'s eye view',
            'capacity' => 80,
            'default_price' => 1000.00, // Price in INR
            'color_code' => '#3498DB',
            'display_order' => 3,
            'is_active' => true
        ]);

        // Create multiple shows
        $shows = [
            [
                'title' => 'Bollywood Spectacular Night',
                'description' => 'An amazing evening of Bollywood music, dance, and entertainment featuring top artists',
                'short_description' => 'Ultimate Bollywood entertainment experience',
                'start_date' => Carbon::now()->addDays(15)->setTime(19, 30),
                'end_date' => Carbon::now()->addDays(15)->setTime(22, 30),
                'duration' => 180, // 3 hours in minutes
                'age_restriction' => 'All ages welcome',
                'price' => 1500.00,
                'performers' => 'Shreya Ghoshal, Rahat Fateh Ali Khan, Dance Troupe Mumbai',
            ],
            [
                'title' => 'Classical Fusion Evening',
                'description' => 'A mesmerizing blend of classical Indian music with contemporary arrangements',
                'short_description' => 'Classical meets contemporary',
                'start_date' => Carbon::now()->addDays(30)->setTime(18, 00),
                'end_date' => Carbon::now()->addDays(30)->setTime(21, 00),
                'duration' => 180,
                'age_restriction' => '12+ recommended',
                'price' => 1200.00,
                'performers' => 'Pandit Jasraj Tribute Band, Kathak Dancers',
            ],
            [
                'title' => 'Sufi Night Under Stars',
                'description' => 'Soulful Sufi music performance in an intimate setting',
                'short_description' => 'Mystical Sufi music experience',
                'start_date' => Carbon::now()->addDays(45)->setTime(20, 00),
                'end_date' => Carbon::now()->addDays(45)->setTime(23, 00),
                'duration' => 180,
                'age_restriction' => '16+ recommended',
                'price' => 2000.00,
                'performers' => 'Kailash Kher, Nizami Brothers',
            ]
        ];

        foreach ($shows as $showData) {
            Show::create([
                'title' => $showData['title'],
                'slug' => null, // Will be auto-generated
                'category_id' => $showCategory->id,
                'venue_id' => $venue->id,
                'description' => $showData['description'],
                'short_description' => $showData['short_description'],
                'featured_image' => null,
                'start_date' => $showData['start_date'],
                'end_date' => $showData['end_date'],
                'price' => $showData['price'],
                'available_tickets' => 400, // Total available across all categories
                'is_featured' => true,
                'status' => 'upcoming',
                'performers' => $showData['performers'],
                'additional_info' => [
                    'dress_code' => 'Smart casual',
                    'parking' => 'Available on-site',
                    'food_beverages' => 'Available for purchase',
                    'photography' => 'Personal photography allowed, no flash',
                    'contact' => 'For queries: tickets@3sixtyshows.com'
                ],
                'duration' => $showData['duration'],
                'age_restriction' => $showData['age_restriction'],
                'is_active' => true,
                'redirect' => false,
                'redirect_url' => null
            ]);
        }

        // Create some test customers
        $customers = [
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh@example.com',
                'phone' => '+91 9876543210',
                'city' => 'Mumbai'
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya@example.com',
                'phone' => '+91 9876543211',
                'city' => 'Delhi'
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit@example.com',
                'phone' => '+91 9876543212',
                'city' => 'Bangalore'
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('✅ Test data seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   • 1 Venue (Mumbai Arena)');
        $this->command->info('   • 3 Seat Categories (Premium, Standard, Economy)');
        $this->command->info('   • ' . Seat::count() . ' Seats');
        $this->command->info('   • 3 General Admission Areas');
        $this->command->info('   • 3 Shows');
        $this->command->info('   • 3 Test Customers');
        $this->command->info('🎭 You can now visit: /shows/1/book to test booking!');
    }
}
