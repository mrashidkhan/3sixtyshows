<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'title', 'video_url', 'thumbnail', 'description',
        'display_order', 'is_featured', 'is_active', 'video_type'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // A video belongs to a show
    public function show()
    {
        return $this->belongsTo(Show::class);
    }
}
