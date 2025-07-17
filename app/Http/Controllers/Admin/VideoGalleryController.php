<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoGallery;
use App\Models\Show;
use Illuminate\Http\Request;

class VideoGalleryController extends Controller
{
    public function index(Request $request)
    {
        $galleries = VideoGallery::when($request->search, function ($query) use ($request) {
            return $query->where('title', 'like', '%' . $request->search . '%');
        })->paginate(10);

        return view('admin.video_gallery.index', compact('galleries'));
    }

    public function create()
    {
        $shows = Show::where('is_active', true)->orderBy('title')->get();
        return view('admin.video_gallery.create', compact('shows'));
    }

    public function store(Request $request)
{
    $request->validate([
        'show_id' => 'nullable|exists:shows,id',
        'title' => 'required|string|max:255',
        'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'description' => 'nullable|string',
        'display_order' => 'nullable|integer',
        'is_featured' => 'nullable|boolean',
        'is_active' => 'nullable|boolean',
    ]);

    $gallery = new VideoGallery();
    $gallery->show_id = $request->show_id;
    $gallery->video_url = $request->video_url;
    $gallery->title = $request->title;
    $gallery->description = $request->description;
    $gallery->display_order = $request->display_order ?? 0;
    $gallery->is_featured = $request->has('is_featured');
    $gallery->is_active = $request->has('is_active');

    if ($request->hasFile('thumbnail')) {
        $path = $request->file('thumbnail')->store('photos', 'public');
        $gallery->thumbnail = $path;
    }

    $gallery->save();

    return redirect()->route('videogallery.list')->with('success', 'Gallery created successfully.');
}

    public function show($id)
    {
        $gallery = VideoGallery::findOrFail($id);
        return view('admin.video_gallery.show', compact('gallery'));
    }

    public function edit($id)
{
    $gallery = VideoGallery::findOrFail($id);
    $shows = Show::where('is_active', true)->orderBy('title')->get();
    return view('admin.video_gallery.edit', compact('gallery', 'shows'));
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'show_id' => 'nullable|exists:shows,id',
            'title' => 'required|string|max:255',
            // 'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $gallery = VideoGallery::findOrFail($id);
        $gallery->title = $request->title;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('photos', 'public');
            $gallery->thumbnail = $path;
        }

        $gallery->save();

        return redirect()->route('videogallery.list')->with('success', 'Video Gallery updated successfully.');
    }

    public function destroy($id)
    {
        $gallery = VideoGallery::findOrFail($id);
        // $gallery->images()->delete(); // Delete associated images
        $gallery->delete();

        return redirect()->route('videogallery.list')->with('success', 'Gallery deleted successfully.');
    }
}
