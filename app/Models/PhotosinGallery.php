<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotosinGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_gallery_id', // Foreign key to PhotoGallery
        'image',            // Path to the image
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
}
