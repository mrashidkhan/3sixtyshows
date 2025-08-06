<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Show;
use App\Models\ShowCategory;
use App\Models\Venue;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class ShowController extends Controller
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
        $venues = Venue::all();
        $categories = ShowCategory::all();
        $shows = Show::all();

        return view('admin.show.index', compact('venues','categories','shows'));
    }


    public function create()
    {
        $categories = ShowCategory::where('is_active', 1)->orderBy('name')->get();
        $venues = Venue::all();
        return view('admin.show.add', compact('venues','categories'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:show_categories,id',
        'venue_id' => 'required|exists:venues,id',
        'description' => 'required|string',
        'short_description' => 'nullable|string|max:500',
        'featured_image' => 'required|image|mimes:jpeg,png,webp,jpg|max:2048',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'price' => 'nullable|numeric|min:0',
        'available_tickets' => 'nullable|integer|min:0',
        'is_featured' => 'boolean',
        'status' => 'required|in:upcoming,ongoing,past,cancelled',
        'performers' => 'nullable|string',
        'duration' => 'nullable|string|max:50',
        'age_restriction' => 'nullable|string|max:50',
        'is_active' => 'boolean',
        'redirect' => 'boolean',
        'redirect_url' => 'nullable|url|required_if:redirect,1',
    ]);

    // Simple image upload without manipulation
    if ($request->hasFile('featured_image')) {
        $file = $request->file('featured_image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path=$file->storeAs('shows', $filename);
        $validated['featured_image'] = $path;
    }

    // Create show
    $show = Show::create($validated);

    return redirect()->route('show.index')
                    ->with('success', 'Show created successfully!');
}

    public function show($id)
    {
        $show = Show::with(['category', 'venue', 'gallery'])
                   ->findOrFail($id);

        return view('admin.show.show', compact('show'));
    }

    public function edit($id)
    {
        $show = Show::findOrFail($id);
        // Get active categories and venues for dropdowns
        $categories = ShowCategory::where('is_active', 1)->orderBy('name')->get();
        // $venues = Venue::where('is_active', 1)->orderBy('name')->get();
        $venues = Venue::all();

        return view('admin.show.edit', compact('show', 'categories', 'venues'));
    }

    public function update(Request $request, $id)
{
    $show = Show::findOrFail($id);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:shows,slug,' . $id,
        'category_id' => 'required|exists:show_categories,id',
        'venue_id' => 'required|exists:venues,id',
        'description' => 'required|string',
        'short_description' => 'required|string|max:500',
        'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'price' => 'required|numeric|min:0',
        'available_tickets' => 'nullable|integer|min:0',
        'is_featured' => 'required|boolean',
        'status' => 'required|in:upcoming,ongoing,past,cancelled',
        'performers' => 'nullable|string',
        'duration' => 'nullable|string|max:50',
        'age_restriction' => 'nullable|string|max:50',
        'is_active' => 'required|boolean',
        'redirect' => 'boolean',
        'redirect_url' => 'nullable|url|required_if:redirect,1', // URL required if redirect is enabled
    ]);

    // Handle the additional_info array (from additional_info_json hidden field)
    if ($request->has('additional_info_json')) {
        $validated['additional_info'] = json_decode($request->additional_info_json, true);
    } elseif ($request->has('additional_info')) {
        // Fallback in case the JavaScript processing didn't work
        $lines = explode("\n", $request->additional_info);
        $additionalInfo = [];

        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                if (!empty($key) && !empty($value)) {
                    $additionalInfo[$key] = $value;
                }
            }
        }

        $validated['additional_info'] = $additionalInfo;
    }

    // Handle image upload if present
    if ($request->hasFile('featured_image')) {
        // Delete old image if exists
        if ($show->featured_image) {
            Storage::delete('public/shows/' . $show->featured_image);
        }

        // Upload new image
        $image = $request->file('featured_image');
        $filename = time() . '.' . $image->getClientOriginalExtension();

        // Store image
        $path = $image->storeAs('shows', $filename);

        // Save filename to database
        $validated['featured_image'] = $path;
    }

    // Clear redirect URL if redirect is disabled
    if (!$request->has('redirect') || !$request->redirect) {
        $validated['redirect_url'] = null;
    }

    // Update show
    $show->update($validated);

    // Update the show status based on dates
    $show->updateStatus();

    return redirect()->route('show.index')
                    ->with('success', 'Show updated successfully!');
}

    public function destroy($id)
    {
        $show = Show::findOrFail($id);

        // Delete images
        if ($show->featured_image) {
            Storage::delete([
                'public/shows/' . $show->featured_image
            ]);
        }

        // Delete associated gallery images
        foreach ($show->gallery as $galleryItem) {
            Storage::delete([
                'public/gallery/' . $galleryItem->image
            ]);
            $galleryItem->delete();
        }

        // Delete show
        $show->delete();

        return redirect()->route('show.index')
                        ->with('success', 'Show deleted successfully!');
    }
}
