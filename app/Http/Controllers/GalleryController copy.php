<?php

// Add this method to your GalleryController or create a new one

namespace App\Http\Controllers;

use App\Models\PhotoGallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Get photos for a specific gallery
     */
    public function getGalleryPhotos($galleryId)
    {
        try {
            $gallery = PhotoGallery::with(['photos' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('display_order', 'asc')
                      ->orderBy('created_at', 'desc');
            }])->findOrFail($galleryId);

            $photos = $gallery->photos->map(function($photo) {
                return [
                    'id' => $photo->id,
                    'description' => $photo->description,
                    'image_url' => $photo->image_with_fallback, // This uses the accessor from your model
                ];
            });

            return response()->json([
                'success' => true,
                'photos' => $photos,
                'gallery_title' => $gallery->title,
                'gallery_description' => $gallery->description,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery not found or error loading photos.',
                'photos' => []
            ], 404);
        }
    }

    /**
     * Display the photo galleries page
     */
    public function index()
    {
        // Get all shows with their photo galleries and photos
        $shows = \App\Models\Show::with(['photos' => function($query) {
            $query->where('is_active', true)
                  ->with(['photos' => function($photoQuery) {
                      $photoQuery->where('is_active', true);
                  }])
                  ->orderBy('display_order', 'asc');
        }])
        ->whereHas('photos', function($query) {
            $query->where('is_active', true);
        })
        ->orderBy('start_date', 'desc')
        ->get();

        return view('galleries.index', compact('shows'));
    }
}
