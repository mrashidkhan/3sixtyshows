{{-- resources/views/bookings/my.blade.php --}}
@extends('layouts.master')

@section('title', 'My Bookings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">My Bookings</h1>

        <!-- Filter Options -->
        <div class="flex space-x-4">
            <select id="status-filter" class="border rounded-lg px-4 py-2 text-sm">
                <option value="">All Bookings</option>
                <option value="confirmed">Confirmed</option>
                <option value="pending">Pending</option>
                <option value="cancelled">Cancelled</option>
                <option value="expired">Expired</option>
            </select>

            <select id="date-filter" class="border rounded-lg px-4 py-2 text-sm">
                <option value="">All Dates</option>
                <option value="upcoming">Upcoming Shows</option>
                <option value="past">Past Shows</option>
                <option value="this-month">This Month</option>
                <option value="last-month">Last Month</option>
            </select>
        </div>
    </div>

    <div class="space-y-6">
        @forelse($bookings as $booking)
            <div class="bg-white border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $booking->show->title }}</h3>
                        <div class="flex items-center text-gray-600 mt-1">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $booking->show->start_date->format('M d, Y \a\t g:i A') }}
                        </div>
                        <div class="flex items-center text-gray-600 mt-1">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $booking->show->venue->name }}
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Booking #{{ $booking->booking_number }}</p>
                        <p class="text-xs text-gray-400">Booked on {{ $booking->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($booking->status === 'expired') bg-gray-100 text-gray-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>

                        @if($booking->payment_status)
                            <div class="mt-2">
                                <span class="inline-block px-2 py-1 rounded text-xs
                                    @if($booking->payment_status === 'completed') bg-green-50 text-green-700 border border-green-200
                                    @elseif($booking->payment_status === 'pending') bg-yellow-50 text-yellow-700 border border-yellow-200
                                    @elseif($booking->payment_status === 'failed') bg-red-50 text-red-700 border border-red-200
                                    @elseif($booking->payment_status === 'refunded') bg-purple-50 text-purple-700 border border-purple-200
                                    @else bg-gray-50 text-gray-700 border border-gray-200 @endif">
                                    Payment {{ ucfirst($booking->payment_status) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Tickets</p>
                        <p class="font-medium">{{ $booking->total_tickets }} {{ Str::plural('ticket', $booking->total_tickets) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="font-medium text-lg">${{ number_format($booking->total_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Booking Date</p>
                        <p class="font-medium">{{ $booking->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Show Status</p>
                        <p class="font-medium
                            @if($booking->show->start_date->isPast()) text-gray-600
                            @elseif($booking->show->start_date->isToday()) text-orange-600
                            @else text-green-600 @endif">
                            @if($booking->show->start_date->isPast())
                                Completed
                            @elseif($booking->show->start_date->isToday())
                                Today
                            @else
                                Upcoming
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Seat Details -->
                @if($booking->bookingItems->count() > 0)
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-2">Seat Details:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($booking->bookingItems as $item)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-50 text-blue-700 border border-blue-200">
                                    @if($item->seat)
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $item->seat_identifier }}
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $item->display_name }} ({{ $item->quantity }}x)
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('bookings.show', $booking) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        View Details
                    </a>

                    @if($booking->status === 'confirmed')
                        <a href="{{ route('tickets.download', $booking) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Download Tickets
                        </a>

                        <a href="{{ route('tickets.pdf', $booking) }}"
                           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm3 5a1 1 0 000 2h1v2a1 1 0 102 0V9h1a1 1 0 100-2H7zM4 13a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Download PDF
                        </a>
                    @endif

                    @if(in_array($booking->status, ['pending', 'confirmed']) && $booking->show->start_date->isFuture())
                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline-block"
                              onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Cancel Booking
                            </button>
                        </form>
                    @endif

                    <!-- Resend Confirmation -->
                    @if($booking->status === 'confirmed')
                        <button onclick="resendConfirmation({{ $booking->id }})"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            Resend Email
                        </button>
                    @endif
                </div>

                <!-- Payment Information -->
                @if($booking->payment_reference)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">
                            Payment Reference: {{ $booking->payment_reference }}
                            @if($booking->payment_method)
                                | Method: {{ ucfirst($booking->payment_method) }}
                            @endif
                        </p>
                    </div>
                @endif

                <!-- Show upcoming show alerts -->
                @if($booking->status === 'confirmed' && $booking->show->start_date->isToday())
                    <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-orange-800 font-medium">Show is today! Don't forget to bring your tickets.</p>
                        </div>
                    </div>
                @elseif($booking->status === 'confirmed' && $booking->show->start_date->isTomorrow())
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-blue-800 font-medium">Show is tomorrow! Make sure you have your tickets ready.</p>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-lg border">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 5a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                <p class="text-gray-500 mb-6">You haven't made any bookings yet. Start by browsing our shows!</p>
                {{-- <a href="{{ route('shows.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Browse Shows
                </a> --}}
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
// Filter functionality
document.getElementById('status-filter').addEventListener('change', function() {
    filterBookings();
});

document.getElementById('date-filter').addEventListener('change', function() {
    filterBookings();
});

function filterBookings() {
    const statusFilter = document.getElementById('status-filter').value;
    const dateFilter = document.getElementById('date-filter').value;

    // Add your filtering logic here or submit form to server
    const url = new URL(window.location);

    if (statusFilter) {
        url.searchParams.set('status', statusFilter);
    } else {
        url.searchParams.delete('status');
    }

    if (dateFilter) {
        url.searchParams.set('date', dateFilter);
    } else {
        url.searchParams.delete('date');
    }

    window.location.href = url.toString();
}

// Resend confirmation email
function resendConfirmation(bookingId) {
    if (confirm('Do you want to resend the confirmation email?')) {
        fetch(`/bookings/${bookingId}/resend-confirmation`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Confirmation email sent successfully!');
            } else {
                alert('Failed to send confirmation email. Please try again.');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}

// Auto-refresh page for today's shows
@if($bookings->where('show.start_date', '>=', now()->startOfDay())->where('show.start_date', '<=', now()->endOfDay())->count() > 0)
    setInterval(function() {
        // Refresh page every 5 minutes if there are shows today
        window.location.reload();
    }, 300000);
@endif
</script>
@endpush
@endsection
