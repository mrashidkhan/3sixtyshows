<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Store a newly created contact message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // Store the contact message (you could create a Contact model for this)
            // For now, we'll just send an email and log it

            // Send email to admin
            $adminEmail = config('mail.admin_email', 'admin@3sixtyshows.com');

            $emailData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? 'Not provided',
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'sent_at' => now()->format('Y-m-d H:i:s')
            ];

            // Send email (you can create a dedicated mailable class for this)
            Mail::send('emails.contact', $emailData, function ($mail) use ($validated, $adminEmail) {
                $mail->to($adminEmail)
                     ->subject('New Contact Form Submission: ' . $validated['subject'])
                     ->replyTo($validated['email'], $validated['name']);
            });

            // Log the contact attempt
            Log::info('Contact form submission', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->route('contact')
                           ->with('success', 'Thank you for your message! We will get back to you soon.');

        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? 'unknown'
            ]);

            return redirect()->route('contact')
                           ->with('error', 'Sorry, there was an error sending your message. Please try again later.')
                           ->withInput();
        }
    }

    /**
     * Send auto-reply to user
     */
    private function sendAutoReply($email, $name)
    {
        try {
            $autoReplyData = [
                'name' => $name,
                'company' => '3Sixty Shows'
            ];

            Mail::send('emails.contact-auto-reply', $autoReplyData, function ($mail) use ($email, $name) {
                $mail->to($email, $name)
                     ->subject('Thank you for contacting 3Sixty Shows')
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            Log::warning('Auto-reply email failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }
}
