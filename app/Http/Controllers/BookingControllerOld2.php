<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TicketingService;
use App\Models\Show;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    protected $ticketingService;

    public function __construct(TicketingService $ticketingService)
    {
        $this->ticketingService = $ticketingService;
    }

    /**
     * Get available seating for a show
     * GET /api/shows/{show}/seating
     */
    public function getAvailableSeating(Show $show)
    {
        try {
            $seating = $this->ticketingService->getAvailableSeating($show);

            return response()->json([
                'success' => true,
                'data' => [
                    'show' => [
                        'id' => $show->id,
                        'title' => $show->title,
                        'start_date' => $show->start_date,
                        'venue' => $show->venue->name,
                    ],
                    'seating_options' => $seating,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching seating information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hold tickets temporarily
     * POST /api/shows/{show}/hold-tickets
     */
    public function holdTickets(Request $request, Show $show)
    {
        $validator = Validator::make($request->all(), [
            'tickets' => 'required|array|min:1',
            'tickets.*.type' => 'required|in:assigned_seat,general_admission',
            'tickets.*.seat_id' => 'required_if:tickets.*.type,assigned_seat|exists:seats,id',
            'tickets.*.area_id' => 'required_if:tickets.*.type,general_admission|exists:general_admission_areas,id',
            'tickets.*.quantity' => 'required_if:tickets.*.type,general_admission|integer|min:1|max:10',
            'tickets.*.ticket_type_id' => 'nullable|exists:ticket_types,id',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $holds = $this->ticketingService->holdTickets(
                $show,
                $request->tickets,
                $request->customer_id,
                session()->getId()
            );

            return response()->json([
                'success' => true,
                'message' => 'Tickets held successfully',
                'data' => [
                    'holds' => $holds,
                    'expires_at' => $holds[0]->expires_at,
                    'total_amount' => $this->calculateHoldTotal($holds),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create booking from held tickets
     * POST /api/bookings/create
     */
    public function createBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'holds' => 'required|array|min:1',
            'holds.*.id' => 'required|exists:ticket_holds,id',
            'customer_details' => 'required|array',
            'customer_details.name' => 'required|string',
            'customer_details.email' => 'required|email',
            'customer_details.phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = $this->ticketingService->confirmBooking(
                $request->holds,
                $request->customer_id,
                $request->customer_details
            );

            // Calculate fees
            $fees = $booking->booking_fees;

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => [
                    'booking' => $booking->load(['show', 'bookingItems', 'customer']),
                    'fees' => $fees,
                    'payment_required' => true,
                    'payment_deadline' => $booking->expires_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process payment for booking
     * POST /api/bookings/{booking}/payment
     */
    public function processPayment(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:credit_card,debit_card,paypal,stripe',
            'payment_gateway' => 'required|string',
            'gateway_response' => 'required|array',
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if booking is still valid
        if ($booking->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking has expired'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Calculate total with fees
            $fees = $booking->booking_fees;
            $totalAmount = $fees['grand_total'];

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'amount' => $totalAmount,
                'currency' => 'USD', // or your preferred currency
                'payment_method' => $request->payment_method,
                'payment_status' => Payment::STATUS_COMPLETED, // Assuming payment is successful
                'transaction_id' => $request->transaction_id,
                'payment_gateway' => $request->payment_gateway,
                'gateway_response' => $request->gateway_response,
                'paid_at' => now(),
            ]);

            // Update booking status
            $booking->update([
                'status' => Booking::STATUS_CONFIRMED,
                'payment_status' => Booking::PAYMENT_COMPLETED,
                'confirmed_at' => now(),
                'total_price' => $totalAmount,
            ]);

            // Generate tickets
            foreach ($booking->bookingItems as $item) {
                $item->generateTickets();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'booking' => $booking->fresh()->load(['tickets', 'show', 'customer']),
                    'payment' => $payment,
                    'tickets_generated' => true,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking details
     * GET /api/bookings/{booking}
     */
    public function getBooking(Booking $booking)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'booking' => $booking->load([
                    'show',
                    'customer',
                    'bookingItems.seat',
                    'bookingItems.generalAdmissionArea',
                    'tickets',
                    'payments'
                ]),
                'fees' => $booking->booking_fees,
            ]
        ]);
    }

    /**
     * Cancel booking
     * POST /api/bookings/{booking}/cancel
     */
    public function cancelBooking(Booking $booking)
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be cancelled'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Update booking status
            $booking->update([
                'status' => Booking::STATUS_CANCELLED
            ]);

            // Release seat reservations
            $booking->seatReservations()->update([
                'status' => 'cancelled'
            ]);

            // Cancel tickets
            $booking->tickets()->update([
                'status' => 'cancelled'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error cancelling booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer's bookings
     * GET /api/customers/{customerId}/bookings
     */
    public function getCustomerBookings($customerId)
    {
        $bookings = Booking::where('customer_id', $customerId)
            ->with(['show', 'tickets', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings
            ]
        ]);
    }

    /**
     * Helper method to calculate total from holds
     */
    private function calculateHoldTotal($holds)
    {
        $total = 0;

        foreach ($holds as $hold) {
            if ($hold->hold_type === 'assigned_seat') {
                $total += $hold->hold_data['price'];
            } elseif ($hold->hold_type === 'general_admission') {
                $total += $hold->hold_data['total_price'];
            }
        }

        return $total;
    }
}
