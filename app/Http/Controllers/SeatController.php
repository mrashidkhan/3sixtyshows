<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Venue;
use App\Models\SeatCategory;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    // Standard CRUD methods plus specialized methods

    public function index(Request $request)
    {
        $query = Seat::query();

        if ($request->filled('venue')) {
            $query->where('venue_id', $request->venue);
        }

        if ($request->filled('category')) {
            $query->where('seat_category_id', $request->category);
        }

        $seats = $query->with(['venue', 'category'])->paginate(50);
        $venues = Venue::pluck('name', 'id');
        $categories = SeatCategory::pluck('name', 'id');

        return view('admin.seats.index', compact('seats', 'venues', 'categories'));
    }

    // Create, store, edit, update methods

    // Specialized method for bulk seat creation
    public function bulkCreate()
    {
        $venues = Venue::pluck('name', 'id');
        $categories = SeatCategory::pluck('name', 'id');

        return view('admin.seats.bulk-create', compact('venues', 'categories'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'seat_category_id' => 'required|exists:seat_categories,id',
            'section' => 'required|string|max:50',
            'row_start' => 'required|string|max:10',
            'row_end' => 'required|string|max:10',
            'seats_per_row' => 'required|integer|min:1|max:500',
        ]);

        // Logic to create multiple seats based on rows and seats per row
        // This would include handling different alphabetical or numeric row naming patterns

        return redirect()->route('admin.seats.index')
            ->with('success', 'Seats created successfully.');
    }

    // Seat map visualization
    public function mapView(Venue $venue)
    {
        $seats = Seat::where('venue_id', $venue->id)->with('category')->get();
        $categories = SeatCategory::all();

        return view('admin.seats.map', compact('venue', 'seats', 'categories'));
    }

    // Update seat positions on map
    public function updatePositions(Request $request)
    {
        $request->validate([
            'seats' => 'required|array',
            'seats.*.id' => 'required|exists:seats,id',
            'seats.*.x' => 'required|integer',
            'seats.*.y' => 'required|integer',
        ]);

        foreach ($request->seats as $seat) {
            Seat::find($seat['id'])->update([
                'coordinates_x' => $seat['x'],
                'coordinates_y' => $seat['y']
            ]);
        }

        return response()->json(['success' => true]);
    }
}
