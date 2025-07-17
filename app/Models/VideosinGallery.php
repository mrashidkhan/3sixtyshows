<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideosinGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_gallery_id', // Foreign key to videoGallery
        'image',            // Path to the image
        'description',      // Description of the video
        'youtubelink',      // YouTube video link
        'display_order',    // Order in which the video should be displayed
        'is_active',        // Status of the video
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // A video belongs to a video gallery
    public function videoGallery()
    {
        return $this->belongsTo(VideoGallery::class);
    }
}
