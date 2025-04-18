<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShowCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shows()
    {
        return $this->hasMany(Show::class, 'category_id');
    }

    // Auto-generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = $category->slug ?: Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
