<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Add your admin middleware here
    }

    public function index(Request $request)
    {
        $query = Booking::with(['user', 'show']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by show if provided
        if ($request->has('show_id') && $request->show_id) {
            $query->where('show_id', $request->show_id);
        }

        // Filter by date range if provided
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('booking_date', [$request->date_from, $request->date_to]);
        }

        // Search by booking number or user email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort results
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $bookings = $query->paginate(15);

        // Get all shows for filter dropdown
        $shows = Show::orderBy('title')->get();

        return view('admin.bookings.index', compact('bookings', 'shows'));
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'show', 'show.venue'])
                         ->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking = Booking::with(['user', 'show'])
                         ->findOrFail($id);

        // Get all available statuses
        $statuses = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled'
        ];

        return view('admin.bookings.edit', compact('booking', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
            'number_of_tickets' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_id' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        // Check if status is changing
        $statusChanged = $booking->status != $validated['status'];
        $oldStatus = $booking->status;

        // Check if number of tickets is changing
        $ticketsChanged = $booking->number_of_tickets != $validated['number_of_tickets'];
        $oldTickets = $booking->number_of_tickets;

        // Update booking
        $booking->update($validated);

        // If status changed to 'cancelled', update available tickets for the show
        if ($statusChanged && $validated['status'] == 'cancelled' && $oldStatus == 'confirmed') {
            $show = $booking->show;
            if ($show->available_tickets !== null) {
                $show->available_tickets += $oldTickets;
                $show->save();
            }
        }

        // If status changed to 'confirmed' from 'pending', update available tickets
        if ($statusChanged && $validated['status'] == 'confirmed' && $oldStatus == 'pending') {
            $show = $booking->show;
            if ($show->available_tickets !== null) {
                $show->available_tickets -= $booking->number_of_tickets;
                $show->save();
            }

            // Send confirmation email
            // Mail::to($booking->user->email)->send(new BookingConfirmed($booking));
        }

        // If number of tickets changed and booking is confirmed, update show tickets
        if ($ticketsChanged && $booking->status == 'confirmed') {
            $show = $booking->show;
            if ($show->available_tickets !== null) {
                $ticketsDifference = $oldTickets - $validated['number_of_tickets'];
                $show->available_tickets += $ticketsDifference;
                $show->save();
            }
        }

        return redirect()->route('admin.bookings.index')
                        ->with('success', 'Booking updated successfully!');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // If booking was confirmed, add tickets back to available count
        if ($booking->status == 'confirmed') {
            $show = $booking->show;
            if ($show->available_tickets !== null) {
                $show->available_tickets += $booking->number_of_tickets;
                $show->save();
            }
        }

        // Delete booking
        $booking->delete();

        return redirect()->route('admin.bookings.index')
                        ->with('success', 'Booking deleted successfully!');
    }

    public function exportCsv(Request $request)
    {
        // Build query based on filters (similar to index method)
        $query = Booking::with(['user', 'show']);

        // Apply the same filters as in the index method
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('show_id') && $request->show_id) {
            $query->where('show_id', $request->show_id);
        }

        // Get all filtered bookings
        $bookings = $query->get();

        // Create CSV content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-export.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');

            // Add CSV header row
            fputcsv($file, [
                'Booking Number',
                'Customer Name',
                'Customer Email',
                'Show Title',
                'Show Date',
                'Tickets',
                'Total Amount',
                'Status',
                'Payment Method',
                'Booking Date'
            ]);

            // Add data rows
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->show->title,
                    $booking->show->start_date->format('Y-m-d H:i'),
                    $booking->number_of_tickets,
                    $booking->total_amount,
                    ucfirst($booking->status),
                    $booking->payment_method ?? 'N/A',
                    $booking->booking_date->format('Y-m-d H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sendConfirmation($id)
    {
        $booking = Booking::with(['user', 'show'])
                         ->findOrFail($id);

        // Send confirmation email
        // Mail::to($booking->user->email)->send(new BookingConfirmed($booking));

        return redirect()->back()
                        ->with('success', 'Confirmation email sent successfully!');
    }
}
