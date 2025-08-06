<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VenueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Add your admin middleware here
    }

    public function index()
    {
        // $venues = Venue::withCount('shows')
        //               ->orderBy('name')
        //               ->paginate(10);

        // Get all venues with shows count for filter dropdowns
        $query = Venue::withCount('shows');

        $venues = Venue::all();
        $countries = $this->getCountriesList();

        return view('admin.venue.index', compact('venues','countries','query'));
    }

    public function create()
    {
        // List of countries for dropdown
        $countries = $this->getCountriesList();
        // $countries = ['USA', 'UAE'];
        $venues = Venue::all();
        return view('admin.venue.add', compact('venues','countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:venues',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Create venue
        $venue = Venue::create($validated);

        return redirect()->route('venues.index')
                        ->with('success', 'Venue created successfully!');
    }

    public function show($id)
    {
        $venue = Venue::withCount('shows')->findOrFail($id);

        // Get shows in this venue
        $shows = $venue->shows()
                      ->with('category')
                      ->orderBy('start_date', 'desc')
                      ->paginate(10);

        return view('admin.venues.show', compact('venue', 'shows'));
    }

    public function edit($id)
    {
        $venue = Venue::findOrFail($id);

        // List of countries for dropdown
        $countries = $this->getCountriesList();

        return view('Admin.venue.edit', compact('venue', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:venues,slug,' . $id,
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Update venue
        $venue->update($validated);

        return redirect()->route('venues.index')
                        ->with('success', 'Venue updated successfully!');
    }

    public function destroy($id)
    {
        $venue = Venue::withCount('shows')->findOrFail($id);

        // Check if venue has shows
        if ($venue->shows_count > 0) {
            return redirect()->route('admin.venues.index')
                            ->with('error', 'Cannot delete venue because it has associated shows. Please reassign or delete the shows first.');
        }

        // Delete venue
        $venue->delete();

        return redirect()->route('admin.venues.index')
                        ->with('success', 'Venue deleted successfully!');
    }

    /**
     * Get list of countries for dropdown
     *
     * @return array
     */
    private function getCountriesList()
    {
        return [
            'United States' => 'United States',
            'United Kingdom' => 'United Kingdom',
            'Canada' => 'Canada',
            'Australia' => 'Australia',
            'Germany' => 'Germany',
            'France' => 'France',
            'Italy' => 'Italy',
            'Spain' => 'Spain',
            'Japan' => 'Japan',
            'China' => 'China',
            'Brazil' => 'Brazil',
            'Mexico' => 'Mexico',
            'India' => 'India',
            'South Africa' => 'South Africa',
            'Other' => 'Other'
        ];
    }

    /**
     * Fetch coordinates for a venue address via API
     * This would be implemented with geocoding service like Google Maps API
     */
    public function fetchCoordinates(Request $request)
    {
        $address = $request->input('address');
        $city = $request->input('city');
        $country = $request->input('country');

        $fullAddress = "{$address}, {$city}, {$country}";

        // Here you would typically make a geocoding API call
        // For demonstration purposes, we'll return dummy coordinates

        return response()->json([
            'success' => true,
            'latitude' => 40.7128, // Example value (New York)
            'longitude' => -74.0060 // Example value (New York)
        ]);
    }
}
