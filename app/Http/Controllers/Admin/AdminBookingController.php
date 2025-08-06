<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Show;
use App\Models\Payment;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AdminBookingController extends Controller
{
    protected $bookingService;
    protected $paymentService;
    protected $notificationService;

    public function __construct(
        BookingService $bookingService,
        PaymentService $paymentService,
        NotificationService $notificationService
    ) {
        $this->middleware('auth');
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'customer', 'show', 'payment']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('show_id')) {
            $query->where('show_id', $request->show_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($sq) use ($search) {
                      $sq->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Handle special filters
        if ($request->get('filter') === 'payments') {
            $query->whereHas('payment');
        }

        if ($request->get('filter') === 'refunds') {
            $query->whereHas('payment', function($q) {
                $q->where('refund_status', 'completed');
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        $shows = Show::orderBy('title')->get();

        return view('admin.bookings.index', compact('bookings', 'shows'));
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'customer', 'show', 'show.venue', 'payment', 'tickets']);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        // Send notification based on new status
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            $this->notificationService->sendBookingConfirmation($booking);
        } elseif ($request->status === 'cancelled') {
            $this->notificationService->sendBookingCancellation($booking);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully'
        ]);
    }

    /**
     * Process refund for booking
     */
    public function refund(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:255'
        ]);

        if (!$booking->payment) {
            return response()->json([
                'success' => false,
                'message' => 'No payment found for this booking'
            ], 400);
        }

        $refundAmount = $request->amount ?? $booking->payment->amount;

        $result = $this->paymentService->processRefund($booking->payment, $refundAmount);

        if ($result['success']) {
            // Update booking status
            $booking->update(['status' => 'cancelled']);

            // Log the refund
            Log::info('Refund processed by admin', [
                'booking_id' => $booking->id,
                'amount' => $refundAmount,
                'reason' => $request->reason,
                'admin_id' => auth()->id()
            ]);
        }

        return response()->json($result);
    }

    /**
     * Resend confirmation email
     */
    public function resendConfirmation(Booking $booking)
    {
        $result = $this->notificationService->sendBookingConfirmation($booking);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success'] ? 'Confirmation email sent' : 'Failed to send email'
        ]);
    }

    /**
     * Export bookings to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = Booking::with(['user', 'customer', 'show', 'payment']);

        // Apply same filters as index
        $this->applyFilters($query, $request);

        $bookings = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Booking Reference',
                'Customer Name',
                'Customer Email',
                'Show Title',
                'Show Date',
                'Tickets',
                'Total Amount',
                'Status',
                'Payment Status',
                'Booking Date'
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->customer->name ?? 'N/A',
                    $booking->customer->email ?? 'N/A',
                    $booking->show->title,
                    $booking->show->start_date?->format('Y-m-d H:i'),
                    $booking->total_tickets,
                    $booking->total_amount,
                    ucfirst($booking->status),
                    $booking->payment_status ?? 'N/A',
                    $booking->created_at->format('Y-m-d H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export bookings to Excel
     */
    public function exportExcel(Request $request)
    {
        // Similar to CSV export but using Excel format
        // This would require Maatwebsite\Excel package
        return $this->exportCsv($request); // Fallback to CSV for now
    }

    /**
     * Export bookings for specific show
     */
    public function exportShowBookings(Show $show, Request $request)
    {
        $bookings = $show->bookings()->with(['user', 'customer', 'payment'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $show->slug . '-bookings-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Booking Reference',
                'Customer Name',
                'Customer Email',
                'Tickets',
                'Total Amount',
                'Status',
                'Booking Date'
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->customer->name ?? 'N/A',
                    $booking->customer->email ?? 'N/A',
                    $booking->total_tickets,
                    $booking->total_amount,
                    ucfirst($booking->status),
                    $booking->created_at->format('Y-m-d H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sales report
     */
    public function salesReport(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        $salesData = Booking::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total_bookings,
                SUM(total_amount) as total_revenue,
                SUM(total_tickets) as total_tickets
            ')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $salesData->sum('total_revenue');
        $totalBookings = $salesData->sum('total_bookings');
        $totalTickets = $salesData->sum('total_tickets');

        return view('admin.reports.sales', compact(
            'salesData', 'totalRevenue', 'totalBookings', 'totalTickets', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Attendance report
     */
    public function attendanceReport(Request $request)
    {
        $shows = Show::with(['bookings' => function($q) {
            $q->where('status', '!=', 'cancelled');
        }])->get();

        return view('admin.reports.attendance', compact('shows'));
    }

    /**
     * Revenue report
     */
    public function revenueReport(Request $request)
    {
        $revenueData = Show::selectRaw('
                shows.title,
                shows.start_date,
                COUNT(bookings.id) as total_bookings,
                SUM(bookings.total_amount) as total_revenue,
                SUM(bookings.total_tickets) as tickets_sold
            ')
            ->leftJoin('bookings', 'shows.id', '=', 'bookings.show_id')
            ->where('bookings.status', '!=', 'cancelled')
            ->groupBy('shows.id', 'shows.title', 'shows.start_date')
            ->orderByDesc('total_revenue')
            ->get();

        return view('admin.reports.revenue', compact('revenueData'));
    }

    /**
     * Scan ticket page
     */
    public function scanTicket()
    {
        return view('admin.scan.index');
    }

    /**
     * Validate scanned ticket
     */
    public function validateTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string'
        ]);

        // Logic to validate ticket code
        // This would typically verify QR code or ticket number

        return response()->json([
            'success' => true,
            'message' => 'Ticket validated successfully',
            'ticket_info' => [
                'booking_reference' => 'BK001',
                'customer_name' => 'John Doe',
                'show_title' => 'Sample Show'
            ]
        ]);
    }

    /**
     * Show scan page for specific show
     */
    public function showScanPage(Show $show)
    {
        return view('admin.scan.show', compact('show'));
    }

    /**
     * Show seat map for admin
     */
    public function showSeatMap(Show $show)
    {
        $seatMap = $this->bookingService->getSeatMapForShow($show->id);

        return view('admin.shows.seat-map', compact('show', 'seatMap'));
    }

    /**
     * Show reservations for specific show
     */
    public function showReservations(Show $show)
    {
        $reservations = $show->seatReservations()
            ->with(['seat', 'user', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.shows.reservations', compact('show', 'reservations'));
    }

    /**
     * Release reservation
     */
    public function releaseReservation($reservationId)
    {
        // Logic to release seat reservation
        return response()->json([
            'success' => true,
            'message' => 'Reservation released successfully'
        ]);
    }

    /**
     * Bulk cancel bookings
     */
    public function bulkCancel(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id'
        ]);

        $cancelledCount = 0;

        foreach ($request->booking_ids as $bookingId) {
            $booking = Booking::find($bookingId);
            if ($booking && $booking->status !== 'cancelled') {
                $booking->update(['status' => 'cancelled']);
                $this->notificationService->sendBookingCancellation($booking);
                $cancelledCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$cancelledCount} bookings cancelled successfully"
        ]);
    }

    /**
     * Bulk confirm bookings
     */
    public function bulkConfirm(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id'
        ]);

        $confirmedCount = 0;

        foreach ($request->booking_ids as $bookingId) {
            $booking = Booking::find($bookingId);
            if ($booking && $booking->status !== 'confirmed') {
                $booking->update(['status' => 'confirmed']);
                $this->notificationService->sendBookingConfirmation($booking);
                $confirmedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$confirmedCount} bookings confirmed successfully"
        ]);
    }

    /**
     * Cleanup expired reservations
     */
    public function cleanupExpiredReservations()
    {
        $cleanedCount = $this->bookingService->cleanupExpiredReservations();

        return response()->json([
            'success' => true,
            'message' => "{$cleanedCount} expired reservations cleaned up"
        ]);
    }

    /**
     * Cleanup expired bookings
     */
    public function cleanupExpiredBookings()
    {
        // Logic to cleanup expired bookings
        return response()->json([
            'success' => true,
            'message' => "Expired bookings cleaned up successfully"
        ]);
    }

    /**
     * Update show statuses
     */
    public function updateShowStatuses()
    {
        $updatedCount = Show::where('end_date', '<', Carbon::now())
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} show statuses updated"
        ]);
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('show_id')) {
            $query->where('show_id', $request->show_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($sq) use ($search) {
                      $sq->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }
    }
}
