<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'image', 'title', 'description', 'display_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    // Scope for active images
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to order by display_order
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
