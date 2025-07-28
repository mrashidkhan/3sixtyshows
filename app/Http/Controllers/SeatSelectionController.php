<?php
namespace App\Http\Controllers;

use App\Services\SeatManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeatSelectionController extends Controller
{
    private $seatService;

    public function __construct(SeatManagementService $seatService)
    {
        $this->seatService = $seatService;
    }

    public function showSeatMap($showId)
    {
        $seats = $this->seatService->getSeatsForShow($showId);

        return view('frontend.seat-selection', [
            'show_id' => $showId,
            'seats' => $seats,
        ]);
    }

    public function reserveSeats(Request $request)
    {
        $request->validate([
            'show_id' => 'required|exists:shows,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        try {
            $reservations = $this->seatService->reserveSeatsTemporarily(
                $request->show_id,
                $request->seat_ids,
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'reservations' => $reservations,
                'message' => 'Seats reserved for 10 minutes',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getAvailableSeats($showId)
    {
        $seats = $this->seatService->getSeatsForShow($showId);

        return response()->json([
            'seats' => $seats,
            'last_updated' => now()->toISOString(),
        ]);
    }
}
