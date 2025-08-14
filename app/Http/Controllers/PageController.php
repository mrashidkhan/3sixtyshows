<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;
use App\Models\ShowCategory;

use Carbon\Carbon;

class PageController extends Controller
{
    public function index()
    {
        // Get past events where end_date has passed from today
        $pastShows = Show::with(['venue', 'category'])
            ->where('end_date', '<', Carbon::now())
            ->orderBy('end_date', 'desc') // Most recent past events first
            // ->limit(12) // Limit to prevent too many items in carousel
            ->get();
            // ->where('is_active', true)

        // Get recent events (within 3 months from today)
        $recentEvents = Show::with(['venue', 'category'])
            ->where('is_active', true)
            ->where('start_date', '>=', Carbon::now()) // Events starting from today
            ->where('start_date', '<=', Carbon::now()->addMonths(3)) // Within 3 months
            ->orderBy('start_date', 'asc') // Earliest events first
            ->limit(15) // Limit for carousel performance
            ->get();

            // $shows = Show::with(['category', 'venue'])
            //      ->where('is_active', true)
            //      ->orderBy('start_date', 'desc')
            //      ->get();

            $shows = Show::with(['category', 'venue'])
                 ->orderBy('start_date', 'desc')
                 ->get();

    $categories = ShowCategory::where('is_active', true)
                              ->orderBy('name')
                              ->get();

        return view('pages.index', compact('pastShows', 'recentEvents','shows','categories'));
    }

    // public function about()
    // {
    //     $pagetitle = 'About Us';
    //     return view('pages.about',['pagetitle' => $pagetitle]);
    // }

    // public function about()
    // {
    // $pageClass = 'page-banner-section';
    // $pagetitle = 'About Us';

    // return view('pages.about', compact('pageClass', 'pagetitle'));
    // }

    public function aboutus()
    {
    $pageClass = 'page-banner-aboutus';
    $pagetitle = 'About Us';

    return view('pages.aboutus', compact('pageClass', 'pagetitle'));
    }

    // public function contact()
    // {
    //     $pagetitle = 'Contact Us';
    //     return view('pages.contact',['pagetitle' => $pagetitle]);
    // }

    public function contact()
{
    $pageClass = 'page-banner-contactus';
    $pagetitle = 'Contact Us';

    return view('pages.contact', compact('pageClass', 'pagetitle'));
}

public function selection()
    {
        $show = Show::find(1);
        $pagetitle = 'Seat Selection';
        return view('frontend.seat-selection',['pagetitle' => $pagetitle,'show' => '$show']);
    }

    public function gallery()
    {
        $pagetitle = 'Gallery';
        return view('pages.gallery',['pagetitle' => $pagetitle]);
    }

    // public function activeevents()
    // {
    //     $pagetitle = 'Active Events';
    //     return view('pages.activeevents',['pagetitle' => $pagetitle]);
    // }
public function activeEvents() {
    $shows = Show::with(['category', 'venue'])
                 ->where('is_active', true)
                 ->get();

    return view('pages.activeevents', compact('shows'));
}
    //

    public function posters() {
    $shows = Show::with(['category', 'venue'])
                 ->orderBy('start_date', 'desc')
                 ->get();

    $categories = ShowCategory::where('is_active', true)
                              ->orderBy('name')
                              ->get();

    return view('pages.posters', compact('shows', 'categories'));
}


    // public function upcomingposters()
    // {
    //     $pagetitle = 'Upcoming Event Posters';
    //     return view('pages.upcomingposters',['pagetitle' => $pagetitle]);
    // }

    public function upcomingposters()
    {
    $pageClass = 'page-banner-upcomingposters';
    $pagetitle = 'Upcoming Events';

    return view('pages.upcomingposters', compact('pageClass', 'pagetitle'));
    }

    public function service()
    {
        return view('pages.service');
    }

    public function team()
    {
        return view('pages.team');
    }

    public function testimonials()
    {
        return view('pages.testimonials');
    }


    public function privacyPolicy()
    {
        return view('pages.privacy-policy');
    }


    /**
 * Display the details of a specific show.
 *
 * @param  string  $slug
 * @return \Illuminate\Http\Response
 */
public function showDetails($slug)
{
    // Find the show by slug with related models
    $show = Show::with(['venue', 'category'])
        ->where('slug', $slug)
        ->firstOrFail();

    // Increment view count if you have this field
    // $show->increment('views');

    // Get related shows from the same category (optional)
    $relatedShows = Show::with(['venue', 'category'])
        ->where('is_active', true)
        ->where('category_id', $show->category_id)
        ->where('id', '!=', $show->id)
        ->where('start_date', '>=', Carbon::now())
        ->orderBy('start_date', 'asc')
        ->limit(4)
        ->get();

    // Check if the event has passed
    $eventPassed = $show->end_date->isPast();

    // Check if event is sold out
    $isSoldOut = $show->status === 'sold_out' || $show->sold_out;

    // Format dates for display
    $formattedStartDate = $show->start_date->format('l, F j, Y');
    $formattedStartTime = $show->start_date->format('g:i A');
    $formattedEndDate = $show->end_date->format('l, F j, Y');
    $formattedEndTime = $show->end_date->format('g:i A');

    // Set page title
    $pagetitle = $show->title;
    $pageClass = 'page-banner-event-details';

    // Return the view with all necessary data
    return view('pages.show-details', compact(
        'show',
        'relatedShows',
        'eventPassed',
        'isSoldOut',
        'formattedStartDate',
        'formattedStartTime',
        'formattedEndDate',
        'formattedEndTime',
        'pagetitle',
        'pageClass'
    ));
}

/**
 * Display the booking page for a specific show.
 *
 * @param  string  $slug
 * @return \Illuminate\Http\Response
 */
public function showBooking($slug)
{
    // Find the show by slug
    $show = Show::with(['venue', 'category'])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    // Check if event has passed
    if ($show->end_date->isPast()) {
        return redirect()->route('show.details', $slug)
            ->with('error', 'This event has already passed.');
    }

    // Check if event is sold out
    if ($show->status === 'sold_out' || $show->sold_out) {
        return redirect()->route('show.details', $slug)
            ->with('error', 'This event is sold out.');
    }

    // If show has redirect URL, redirect there
    if ($show->redirect && $show->redirect_url) {
        return redirect()->away($show->redirect_url);
    }

    // Otherwise, show booking page
    $pagetitle = 'Book Tickets - ' . $show->title;
    $pageClass = 'page-banner-booking';

    return view('pages.show-booking', compact('show', 'pagetitle', 'pageClass'));
}
}
