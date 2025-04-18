<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Show;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Add your admin middleware here
    }

    public function index()
    {
        // Get counts for dashboard widgets
        $totalShows = Show::count();
        $activeShows = Show::where('is_active', true)->count();
        $upcomingShows = Show::where('status', 'upcoming')->count();

        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        $totalUsers = User::count();

        // Calculate total revenue
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_amount');

        // Get recent bookings
        $recentBookings = Booking::with(['user', 'show'])
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();

        // Get upcoming shows
        $upcomingShowsList = Show::with('venue')
                               ->where('status', 'upcoming')
                               ->orderBy('start_date', 'asc')
                               ->take(5)
                               ->get();

        return view('admin.dashboard', compact(
            'totalShows', 'activeShows', 'upcomingShows',
            'totalBookings', 'confirmedBookings', 'pendingBookings',
            'totalUsers', 'totalRevenue', 'recentBookings', 'upcomingShowsList'
        ));
    }
}
