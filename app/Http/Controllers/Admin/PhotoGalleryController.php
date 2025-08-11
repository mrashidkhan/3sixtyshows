<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoGallery;
use App\Models\PhotosinGallery;
use App\Models\Show;
use Illuminate\Http\Request;

class PhotoGalleryController extends Controller
{
    public function index(Request $request)
    {
        $galleries = PhotoGallery::when($request->search, function ($query) use ($request) {
            return $query->where('title', 'like', '%' . $request->search . '%');
        })->paginate(10);

        return view('admin.photo_gallery.index', compact('galleries'));
    }

//     public function clientIndex()
// {
//     // $photos = PhotosinGallery::paginate(12);
//     // return view('pages.photo-galleries', compact('photos'));
//     $photos = PhotosinGallery::all();


//     return view('pages.photo-galleries', compact('photos'));
// }

public function clientIndex()
{
    $photos = PhotosinGallery::select([
        'id',
        'photo_gallery_id',
        'image',
        'description',
        'display_order',
        'created_at'
    ])
    ->with([
        'photoGallery' => function($query) {
            $query->select('id', 'title', 'description', 'is_featured', 'show_id')
                  ->where('is_active', true);
        },
        'photoGallery.show' => function($query) {
            $query->select('id', 'title', 'start_date', 'end_date', 'venue_id');
        },
        'photoGallery.show.venue' => function($query) {
            $query->select('id', 'name');
        }
    ])
    ->active() // Only active photos
    ->withActiveGallery() // Only photos from active galleries
    ->withImages() // Only photos that have images
    ->latest()
    ->paginate(12);

    return view('pages.photo-galleries', compact('photos'));
}



    public function create()
    {
        // $shows = Show::where('is_active', true)->orderBy('title')->get();
        $shows = Show::orderBy('title')->get();
        return view('admin.photo_gallery.create', compact('shows'));
    }

    public function store(Request $request)
{
    $request->validate([
        'show_id' => 'nullable|exists:shows,id',
        'title' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'description' => 'nullable|string',
        'display_order' => 'nullable|integer',
        'is_featured' => 'nullable|boolean',
        'is_active' => 'nullable|boolean',
    ]);

    $gallery = new PhotoGallery();
    $gallery->show_id = $request->show_id;
    $gallery->title = $request->title;
    $gallery->description = $request->description;
    $gallery->display_order = $request->display_order ?? 0;
    $gallery->is_featured = $request->has('is_featured');
    $gallery->is_active = $request->has('is_active');

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('photos', 'public');
        $gallery->image = $path;
    }

    $gallery->save();

    return redirect()->route('photogallery.list')->with('success', 'Gallery created successfully.');
}

    public function show($id)
    {
        $gallery = PhotoGallery::findOrFail($id);
        return view('admin.photo_gallery.show', compact('gallery'));
    }

    public function edit($id)
{
    $gallery = PhotoGallery::findOrFail($id);
    $shows = Show::where('is_active', true)->orderBy('title')->get();
    return view('admin.photo_gallery.edit', compact('gallery', 'shows'));
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $gallery = PhotoGallery::findOrFail($id);
        $gallery->title = $request->title;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('photos', 'public');
                $gallery->images()->create(['path' => $path]);
            }
        }

        $gallery->save();

        return redirect()->route('photogallery.list')->with('success', 'Gallery updated successfully.');
    }

    public function destroy($id)
    {
        $gallery = PhotoGallery::findOrFail($id);
        $gallery->images()->delete(); // Delete associated images
        $gallery->delete();

        return redirect()->route('photogallery.index')->with('success', 'Gallery deleted successfully.');
    }
}
