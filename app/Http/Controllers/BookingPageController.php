<?php
// app/Http/Controllers/BookingPageController.php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Services\TicketingService;
use Inertia\Inertia;

class BookingPageController extends Controller
{
    protected $ticketingService;

    public function __construct(TicketingService $ticketingService)
    {
        $this->ticketingService = $ticketingService;
    }

    public function showSeatSelection(Show $show)
    {
        // Check if show exists and is active
        if (!$show->is_active) {
            abort(404, 'Show not found or inactive');
        }

        try {
            $seatingOptions = $this->ticketingService->getAvailableSeating($show);

            return Inertia::render('Booking/SeatSelection', [
                'show' => $show->load('venue'),
                'seatingOptions' => $seatingOptions,
            ]);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Seating options error: ' . $e->getMessage());

            // Return error page or redirect with message
            return Inertia::render('Booking/SeatSelection', [
                'show' => $show->load('venue'),
                'seatingOptions' => [
                    'assigned_seating' => [],
                    'general_admission' => [],
                    'show_mode' => 'undefined'
                ],
                'error' => 'Unable to load seating options. Please try again.'
            ]);
        }
    }
}
