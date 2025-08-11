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

            // Debug log
            \Log::info('Gallery found:', [
                'gallery_id' => $galleryId,
                'title' => $gallery->title,
                'photos_count' => $gallery->photos->count()
            ]);

            $photos = $gallery->photos->map(function($photo) {
                // Since you store complete URLs, use the accessor
                $imageUrl = $photo->image_url;

                // Debug each photo
                \Log::info('Photo processed:', [
                    'photo_id' => $photo->id,
                    'raw_image' => $photo->image,
                    'processed_url' => $imageUrl
                ]);

                return [
                    'id' => $photo->id,
                    'description' => $photo->description,
                    'image_url' => $imageUrl,
                    'has_image' => $photo->has_image,
                    // Debug info (remove in production)
                    'debug_raw_image' => $photo->image,
                ];
            });

            return response()->json([
                'success' => true,
                'photos' => $photos,
                'gallery_title' => $gallery->title,
                'gallery_description' => $gallery->description,
                // Debug info (remove in production)
                'debug_info' => [
                    'gallery_id' => $galleryId,
                    'total_photos' => $photos->count(),
                    'first_photo_url' => $photos->first()['image_url'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Gallery photos error:', [
                'gallery_id' => $galleryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gallery not found or error loading photos.',
                'photos' => [],
                'error' => $e->getMessage()
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
