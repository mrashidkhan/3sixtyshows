<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\VideosinGalleryController;
use App\Models\VideoGallery;
use App\Models\VideosinGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideosinGalleryController extends Controller
{
    public function index(Request $request)
{
    $videos = VideosinGallery::when($request->search, function ($query) use ($request) {
            return $query->where('description', 'like', '%' . $request->search . '%');
        })
        ->orderBy('display_order')
        ->paginate(12);

    return view('admin.videos_in_gallery.index', compact('videos'));
}

    public function create()
    {
        $galleries = VideoGallery::where('is_active', true)->orderBy('title')->get();
        return view('admin.videos_in_gallery.create', compact('galleries'));
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        //     'description' => 'nullable|string',
        //     'display_order' => 'nullable|integer',
        //     'is_active' => 'nullable|boolean',
        // ]);

        // $gallery = videoGallery::findOrFail($galleryId);

        // $video = new VideosinGallery();
        // $video->video_gallery_id = $galleryId;
        // $video->description = $request->description;
        // $video->display_order = $request->display_order ?? 0;
        // $video->is_active = $request->has('is_active');

        if ($request->hasFile('image')) {
                $files = $request->file('image');
                foreach ($files as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/videos_in_galleries', $fileName, 'public');
                    $videoFilePath = '/storage/' . $filePath;

                    VideosinGallery::create([
                        'description' => $request->description, // Assuming there is a single description for all videos
                        'video_gallery_id' => $request->video_gallery_id,
                        'image' => $videoFilePath,
                    ]);
                }
            }

        // if ($request->hasFile('image')) {
        //     $path = $request->file('image')->store('gallery_videos', 'public');
        //     $video->image = $path;
        // }

        // $video->save();

        return redirect()->route('videosinGallery.list')
            ->with('success', 'video added to gallery successfully.');
    }

    public function show($galleryId, $id)
    {
        $gallery = VideoGallery::findOrFail($galleryId);
        $video = VideosinGallery::where('video_gallery_id', $galleryId)
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.videos_in_gallery.show', compact('video', 'gallery'));
    }

    public function edit( $id)
    {
        // $gallery = videoGallery::findOrFail($galleryId);
        $video = VideosinGallery::findOrFail($id);


        return view('admin.videos_in_gallery.edit', compact('video'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'description' => 'nullable|string',
        'display_order' => 'nullable|integer',
        'is_active' => 'nullable|boolean',
    ]);

    $video = VideosinGallery::findOrFail($id);

    // Update basic fields
    $video->description = $request->description;
    $video->display_order = $request->display_order;
    $video->is_active = $request->has('is_active');

    // Handle image upload only if a new image is provided
    if ($request->hasFile('image')) {
        // Delete old image if it exists
        if ($video->image && Storage::disk('public')->exists(str_replace('/storage/', '', $video->image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $video->image));
        }

        // Upload new image
        $image = $request->file('image');
        $fileName = time() . '_' . $image->getClientOriginalName();
        $filePath = $image->storeAs('uploads/videos_in_galleries', $fileName, 'public');
        $video->image = '/storage/' . $filePath;
    }
    // If no new image is uploaded, keep the existing image (don't modify $video->image)

    $video->save();

    return redirect()->route('videosingallery.list')
        ->with('success', 'video updated successfully.');
}

    public function destroy($galleryId, $id)
    {
        $video = VideosinGallery::where('video_gallery_id', $galleryId)
            ->where('id', $id)
            ->firstOrFail();

        // Delete the image file
        if ($video->image && Storage::disk('public')->exists($video->image)) {
            Storage::disk('public')->delete($video->image);
        }

        $video->delete();

        return redirect()->route('videos.gallery.index', $galleryId)
            ->with('success', 'video removed from gallery successfully.');
    }

    public function bulkUpload($galleryId)
    {
        $gallery = VideoGallery::findOrFail($galleryId);
        return view('admin.videos_in_gallery.bulk_upload', compact('gallery'));
    }

    public function processBulkUpload(Request $request, $galleryId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_display_order' => 'nullable|integer',
        ]);

        $gallery = VideoGallery::findOrFail($galleryId);
        $defaultOrder = $request->default_display_order ?? 0;

        if ($request->hasFile('images')) {
            $count = 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('gallery_videos', 'public');

                $video = new VideosinGallery();
                $video->video_gallery_id = $galleryId;
                $video->image = $path;
                $video->display_order = $defaultOrder + $count;
                $video->is_active = true;
                $video->save();

                $count++;
            }

            return redirect()->route('videos.gallery.index', $galleryId)
                ->with('success', $count . ' videos added to gallery successfully.');
        }

        return redirect()->back()->with('error', 'No images were uploaded.');
    }

    public function updateOrder(Request $request, $galleryId)
    {
        $request->validate([
            'videos' => 'required|array',
            'videos.*.id' => 'required|exists:videosin_galleries,id',
            'videos.*.order' => 'required|integer',
        ]);

        foreach ($request->videos as $item) {
            $video = VideosinGallery::find($item['id']);
            if ($video && $video->video_gallery_id == $galleryId) {
                $video->display_order = $item['order'];
                $video->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
