<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotosinGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_gallery_id', // Foreign key to PhotoGallery
        'image',            // Complete URL/path to the image
        'description',      // Description of the photo
        'display_order',    // Order in which the photo should be displayed
        'is_active',        // Status of the photo
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // A photo belongs to a photo gallery
    public function photoGallery()
    {
        return $this->belongsTo(PhotoGallery::class);
    }

    // Accessor for image URL - since you store complete URLs
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // If image already contains full URL (starts with http), return as is
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // For paths like /storage/uploads/... or storage/uploads/...
        // Remove leading slash and use asset() helper
        $imagePath = ltrim($this->image, '/');
        return asset($imagePath);
    }

    // Check if image exists
    public function getHasImageAttribute()
    {
        return !empty($this->image);
    }

    // Get image with fallback
    public function getImageWithFallbackAttribute()
    {
        if ($this->has_image) {
            return $this->image_url;
        }

        // Return a simple data URL for a placeholder instead of file path
        return 'data:image/svg+xml;base64,' . base64_encode('
            <svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
                <rect width="400" height="300" fill="#f8f9fa"/>
                <text x="200" y="150" text-anchor="middle" fill="#6c757d" font-family="Arial" font-size="16">No Image Available</text>
            </svg>
        ');
    }

    // Scope for active photos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for photos with active galleries
    public function scopeWithActiveGallery($query)
    {
        return $query->whereHas('photoGallery', function($q) {
            $q->where('is_active', true);
        });
    }

    // Scope for photos with images
    public function scopeWithImages($query)
    {
        return $query->whereNotNull('image')->where('image', '!=', '');
    }
}
