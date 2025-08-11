<?php
// app/Http/Controllers/BookingPageController.php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Services\TicketingService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BookingPageController extends Controller
{
    protected $ticketingService;

    public function __construct(TicketingService $ticketingService)
    {
        $this->ticketingService = $ticketingService;
    }

    public function showSeatSelection(Show $show)
    {
        $seatingOptions = $this->ticketingService->getAvailableSeating($show);

        return Inertia::render('Booking/SeatSelection', [
            'show' => $show->load('venue'),
            'seatingOptions' => $seatingOptions,
        ]);
    }

    public function showCheckout(Request $request)
    {
        $holdIds = explode(',', $request->get('holds', ''));

        // Get hold details (you'll need to implement this)
        // $holds = TicketHold::whereIn('id', $holdIds)->get();

        return Inertia::render('Booking/Checkout', [
            'holds' => $holdIds,
        ]);
    }
}
