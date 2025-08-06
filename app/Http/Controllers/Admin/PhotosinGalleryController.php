<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoGallery;
use App\Models\PhotosinGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotosinGalleryController extends Controller
{
    public function index(Request $request)
{
    $photos = PhotosinGallery::when($request->search, function ($query) use ($request) {
            return $query->where('description', 'like', '%' . $request->search . '%');
        })
        ->orderBy('display_order')
        ->paginate(12);

    return view('admin.photos_in_gallery.index', compact('photos'));
}

    public function create()
    {
        $galleries = PhotoGallery::where('is_active', true)->orderBy('title')->get();
        return view('admin.photos_in_gallery.create', compact('galleries'));
    }

    public function store(Request $request)
    {
        $request->validate([
    'photo_gallery_id' => 'required|exists:photo_galleries,id',
    'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    'description' => 'nullable|string',
    'display_order' => 'nullable|integer',
    'is_active' => 'nullable|boolean',
]);

        // $gallery = PhotoGallery::findOrFail($galleryId);

        // $photo = new PhotosinGallery();
        // $photo->photo_gallery_id = $galleryId;
        // $photo->description = $request->description;
        // $photo->display_order = $request->display_order ?? 0;
        // $photo->is_active = $request->has('is_active');

        if ($request->hasFile('image')) {
                $files = $request->file('image');
                foreach ($files as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/photos_in_galleries', $fileName, 'public');
                    $photoFilePath = '/storage/' . $filePath;

                    PhotosinGallery::create([
                        'description' => $request->description, // Assuming there is a single description for all photos
                        'photo_gallery_id' => $request->photo_gallery_id,
                        'image' => $photoFilePath,
                    ]);
                }
            }

        // if ($request->hasFile('image')) {
        //     $path = $request->file('image')->store('gallery_photos', 'public');
        //     $photo->image = $path;
        // }

        // $photo->save();

        return redirect()->route('photosingallery.list')
            ->with('success', 'Photo added to gallery successfully.');
    }

    public function show($galleryId, $id)
    {
        $gallery = PhotoGallery::findOrFail($galleryId);
        $photo = PhotosinGallery::where('photo_gallery_id', $galleryId)
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.photos_in_gallery.show', compact('photo', 'gallery'));
    }

    public function edit( $id)
    {
        // $gallery = PhotoGallery::findOrFail($galleryId);
        $photo = PhotosinGallery::findOrFail($id);


        return view('admin.photos_in_gallery.edit', compact('photo'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'description' => 'nullable|string',
        'display_order' => 'nullable|integer',
        'is_active' => 'nullable|boolean',
    ]);

    $photo = PhotosinGallery::findOrFail($id);

    // Update basic fields
    $photo->description = $request->description;
    $photo->display_order = $request->display_order;
    $photo->is_active = $request->has('is_active');

    // Handle image upload only if a new image is provided
    if ($request->hasFile('image')) {
        // Delete old image if it exists
        if ($photo->image && Storage::disk('public')->exists(str_replace('/storage/', '', $photo->image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $photo->image));
        }

        // Upload new image
        $image = $request->file('image');
        $fileName = time() . '_' . $image->getClientOriginalName();
        $filePath = $image->storeAs('uploads/photos_in_galleries', $fileName, 'public');
        $photo->image = '/storage/' . $filePath;
    }
    // If no new image is uploaded, keep the existing image (don't modify $photo->image)

    $photo->save();

    return redirect()->route('photosingallery.list')
        ->with('success', 'Photo updated successfully.');
}

    public function destroy($galleryId, $id)
    {
        $photo = PhotosinGallery::where('photo_gallery_id', $galleryId)
            ->where('id', $id)
            ->firstOrFail();

        // Delete the image file
        if ($photo->image && Storage::disk('public')->exists($photo->image)) {
            Storage::disk('public')->delete($photo->image);
        }

        $photo->delete();

        return redirect()->route('photos.gallery.index', $galleryId)
            ->with('success', 'Photo removed from gallery successfully.');
    }

    public function bulkUpload($galleryId)
    {
        $gallery = PhotoGallery::findOrFail($galleryId);
        return view('admin.photos_in_gallery.bulk_upload', compact('gallery'));
    }

    public function processBulkUpload(Request $request, $galleryId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_display_order' => 'nullable|integer',
        ]);

        $gallery = PhotoGallery::findOrFail($galleryId);
        $defaultOrder = $request->default_display_order ?? 0;

        if ($request->hasFile('images')) {
            $count = 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('gallery_photos', 'public');

                $photo = new PhotosinGallery();
                $photo->photo_gallery_id = $galleryId;
                $photo->image = $path;
                $photo->display_order = $defaultOrder + $count;
                $photo->is_active = true;
                $photo->save();

                $count++;
            }

            return redirect()->route('photos.gallery.index', $galleryId)
                ->with('success', $count . ' photos added to gallery successfully.');
        }

        return redirect()->back()->with('error', 'No images were uploaded.');
    }

    public function updateOrder(Request $request, $galleryId)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:photosin_galleries,id',
            'photos.*.order' => 'required|integer',
        ]);

        foreach ($request->photos as $item) {
            $photo = PhotosinGallery::find($item['id']);
            if ($photo && $photo->photo_gallery_id == $galleryId) {
                $photo->display_order = $item['order'];
                $photo->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
