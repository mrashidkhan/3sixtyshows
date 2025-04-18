<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\ShowCategory;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShowController extends Controller
{
    /**
     * Display a listing of the shows.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//     public function index(Request $request)
//     {
//         // Start with base query
//         $query = Show::with(['category', 'venue']);

//         // Apply filters if provided
//         if ($request->filled('category')) {
//             $query->where('category_id', $request->category);
//         }

//         if ($request->filled('status')) {
//             $query->where('status', $request->status);
//         }

//         if ($request->filled('is_active')) {
//             $query->where('is_active', $request->is_active);
//         }

//         if ($request->filled('search')) {
//             $query->where('title', 'like', '%' . $request->search . '%');
//         }

//         // Order by start date (newest first)
//         $query->orderBy('start_date', 'desc');

//         // Paginate the results
//         $shows = $query->paginate(10);

//         // Get categories for filter dropdown
//         $categories = ShowCategory::where('is_active', 1)->orderBy('name')->get();

// //         session()->flash('shows', $shows);
// // session()->flash('categories', $categories);
// // return redirect()->route('show.index');
//     }

    public function index()
    {
        // $venues = Venue::withCount('shows')
        //               ->orderBy('name')
        //               ->paginate(10);
        $venues = Venue::all();
        $categories = ShowCategory::all();
        $shows = Show::all();

        return view('admin.show.index', compact('venues','categories','shows'));
    }


    /**
     * Show the form for creating a new show.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     // Get active categories and venues for dropdowns
    //     $categories = ShowCategory::where('is_active', 1)->orderBy('name')->get();
    //     $venues = Venue::where('is_active', 1)->orderBy('name')->get();

    //     return view('admin.shows.create', compact('categories', 'venues'));
    // }

    public function create()
    {
        $categories = ShowCategory::where('is_active', 1)->orderBy('name')->get();
        $venues = Venue::all();
        return view('admin.show.add', compact('venues','categories'));
    }

    /**
     * Store a newly created show in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:shows',
            'category_id' => 'required|exists:show_categories,id',
            'venue_id' => 'required|exists:venues,id',
            'description' => 'required|string',
            'short_description' => 'required|string|max:255',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'available_tickets' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'status' => 'required|in:upcoming,ongoing,past,cancelled',
            'duration' => 'nullable|string|max:100',
            'age_restriction' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image upload
        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            $imagePath = $request->file('featured_image')->store('shows', 'public');
            $validated['featured_image'] = $imagePath;
        }

        // Handle performers array (convert from textarea)
        if ($request->filled('performers_json')) {
            $validated['performers'] = json_decode($request->performers_json, true);
        } elseif ($request->filled('performers')) {
            $performers = explode("\n", $request->performers);
            $validated['performers'] = array_map('trim', array_filter($performers));
        } else {
            $validated['performers'] = [];
        }

        // Handle additional info array (convert from textarea)
        if ($request->filled('additional_info_json')) {
            $validated['additional_info'] = json_decode($request->additional_info_json, true);
        } elseif ($request->filled('additional_info')) {
            $lines = explode("\n", $request->additional_info);
            $infoArray = [];

            foreach ($lines as $line) {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    if (!empty($key) && !empty($value)) {
                        $infoArray[$key] = $value;
                    }
                }
            }

            $validated['additional_info'] = $infoArray;
        } else {
            $validated['additional_info'] = [];
        }

        // Create the show
        $show = Show::create($validated);

        return redirect()->route('show.index')
            ->with('success', 'Show created successfully.');
    }

    /**
     * Show the form for editing the specified show.
     *
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $show = Show::findOrFail($id);
        // Get active categories and venues for dropdowns
        $categories = ShowCategory::where('is_active', 1)->orderBy('name')->get();
        // $venues = Venue::where('is_active', 1)->orderBy('name')->get();
        $venues = Venue::all();

        return view('admin.show.edit', compact('show', 'categories', 'venues'));
    }

    /**
     * Update the specified show in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Show $show)
    {
        // Validate the request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shows,slug,' . $show->id,
            'category_id' => 'required|exists:show_categories,id',
            'venue_id' => 'required|exists:venues,id',
            'description' => 'required|string',
            'short_description' => 'required|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'available_tickets' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'status' => 'required|in:upcoming,ongoing,past,cancelled',
            'duration' => 'nullable|string|max:100',
            'age_restriction' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        // Handle image upload if a new one is provided
        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            // Delete old image if exists
            if ($show->featured_image && Storage::disk('public')->exists($show->featured_image)) {
                Storage::disk('public')->delete($show->featured_image);
            }

            // Store the new image
            $imagePath = $request->file('featured_image')->store('shows', 'public');
            $validated['featured_image'] = $imagePath;
        }

        // Handle performers array (convert from textarea)
        if ($request->filled('performers_json')) {
            $validated['performers'] = json_decode($request->performers_json, true);
        } elseif ($request->filled('performers')) {
            $performers = explode("\n", $request->performers);
            $validated['performers'] = array_map('trim', array_filter($performers));
        } else {
            $validated['performers'] = [];
        }

        // Handle additional info array (convert from textarea)
        if ($request->filled('additional_info_json')) {
            $validated['additional_info'] = json_decode($request->additional_info_json, true);
        } elseif ($request->filled('additional_info')) {
            $lines = explode("\n", $request->additional_info);
            $infoArray = [];

            foreach ($lines as $line) {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    if (!empty($key) && !empty($value)) {
                        $infoArray[$key] = $value;
                    }
                }
            }

            $validated['additional_info'] = $infoArray;
        } else {
            $validated['additional_info'] = [];
        }

        // Update the show
        $show->update($validated);

        // Update show status based on dates
        $show->updateStatus();

        return redirect()->route('show.index')
            ->with('success', 'Show updated successfully.');
    }

    /**
     * Remove the specified show from storage.
     *
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function destroy(Show $show)
    {
        // Delete the featured image if it exists
        if ($show->featured_image && Storage::disk('public')->exists($show->featured_image)) {
            Storage::disk('public')->delete($show->featured_image);
        }

        // Delete the show
        $show->delete();

        return redirect()->route('show.index')
            ->with('success', 'Show deleted successfully.');
    }

    /**
     * Display the specified show.
     *
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function show(Show $show)
    {
        $show->load(['category', 'venue', 'gallery', 'bookings']);
        return view('admin.shows.show', compact('show'));
    }
}
