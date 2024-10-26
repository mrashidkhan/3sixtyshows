<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function automation()
    {
        return view('services.automation');
    }

    public function itservice()
    {
        return view('services.itservice');
    }

    public function itsolutions()
    {
        return view('services.itsolutions');
    }

    public function softwaredevelopment()
    {
        return view('services.softwaredevelopment');
    }

    public function staffing()
    {
        return view('services.staffing');
    }

    public function training()
    {
        return view('services.training');
    }
}
