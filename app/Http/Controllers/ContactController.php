<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'nullable|string|max:255',
        'message' => 'nullable|string',
    ]);

    // Prepare the data for the email
    $contactData = [
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'subject' => $request->input('subject'),
        'message' => $request->input('message'),
    ];

    // Send the email
    Mail::to("muhammadrashidkhan800@gmail.com")->send(new ContactMail($contactData));

    return redirect()->route('contact', ['#sentmessage'])->with('info', 'Your message has been sent successfully!');
}

}
