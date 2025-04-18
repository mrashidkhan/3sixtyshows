<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Show extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'category_id', 'venue_id', 'description',
        'short_description', 'featured_image', 'start_date', 'end_date',
        'price', 'available_tickets', 'is_featured', 'status',
        'performers', 'additional_info', 'duration', 'age_restriction', 'is_active'
    ];

    protected $casts = [
        'performers' => 'array',
        'additional_info' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ShowCategory::class, 'category_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function tickets()
{
    return $this->hasMany(Ticket::class);
}

    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }

    // One show has many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // One show has many posters
public function posters()
{
    return $this->hasMany(Poster::class);
}

// One show has many photos
public function photos()
{
    return $this->hasMany(PhotoGallery::class);
}

// One show has many videos
public function videos()
{
    return $this->hasMany(VideoGallery::class);
}

public function ticketTypes()
{
    return $this->hasMany(TicketType::class);
}


    // Get sold tickets count
    public function getSoldTicketsAttribute()
    {
        return $this->bookings()->where('status', 'confirmed')->sum('number_of_tickets');
    }

    // Check if show is sold out
    public function getSoldOutAttribute()
    {
        if ($this->available_tickets === null) {
            return false;
        }

        return $this->sold_tickets >= $this->available_tickets;
    }

    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        if ($this->price === null || $this->price == 0) {
            return 'Free';
        }

        return '$' . number_format($this->price, 2);
    }

    // Get show status based on dates
    public function updateStatus()
    {
        $now = Carbon::now();

        if ($this->status === 'cancelled') {
            return; // Keep cancelled status
        }

        if ($this->end_date && $now > $this->end_date) {
            $this->status = 'past';
        } elseif ($now > $this->start_date) {
            $this->status = 'ongoing';
        } else {
            $this->status = 'upcoming';
        }

        $this->save();
    }

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($show) {
            $show->slug = $show->slug ?: Str::slug($show->title);
        });

        static::updating(function ($show) {
            if ($show->isDirty('title') && !$show->isDirty('slug')) {
                $show->slug = Str::slug($show->title);
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopePast($query)
    {
        return $query->where('status', 'past');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    // A show has many seat reservations
public function seatReservations()
{
    return $this->hasMany(SeatReservation::class);
}

// Get available seats for this show
public function getAvailableSeatsAttribute()
{
    // Get all seat IDs that are reserved for this show
    $reservedSeatIds = $this->seatReservations()
        ->whereIn('status', ['booked', 'reserved'])
        ->pluck('seat_id');

    // Get venue's seats excluding the reserved ones
    return Seat::where('venue_id', $this->venue_id)
        ->where('is_active', true)
        ->whereNotIn('id', $reservedSeatIds)
        ->get();
}
}
