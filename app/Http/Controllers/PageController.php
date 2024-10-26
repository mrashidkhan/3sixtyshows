<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        return view('pages.index');
    }

    // public function about()
    // {
    //     $pagetitle = 'About Us';
    //     return view('pages.about',['pagetitle' => $pagetitle]);
    // }

    public function about()
    {
    $pageClass = 'page-banner-section';
    $pagetitle = 'About Us';

    return view('pages.about', compact('pageClass', 'pagetitle'));
    }

    public function aboutus()
    {
    $pageClass = 'page-banner-aboutus';
    $pagetitle = 'About Us';

    return view('pages.about', compact('pageClass', 'pagetitle'));
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

    public function gallery()
    {
        $pagetitle = 'Gallery';
        return view('pages.gallery',['pagetitle' => $pagetitle]);
    }

    public function events()
    {
        $pagetitle = 'Past Events';
        return view('pages.events',['pagetitle' => $pagetitle]);
    }

    //

    public function posters()
{
    $pageClass = 'page-banner-pastevents';
    $pagetitle = 'Past Events';

    return view('pages.posters', compact('pageClass', 'pagetitle'));
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
}
