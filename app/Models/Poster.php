<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'title', 'image', 'description',
        'display_order', 'is_featured', 'is_active'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // A poster belongs to a show
    public function show()
    {
        return $this->belongsTo(Show::class);
    }
}
