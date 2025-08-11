import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import { MapPin, Users, Clock, CreditCard, CheckCircle } from 'lucide-react';

const SeatSelection = ({ show, seatingOptions }) => {
    const [selectedSeats, setSelectedSeats] = useState([]);
    const [selectedGA, setSelectedGA] = useState({});
    const [loading, setLoading] = useState(false);
    const [step, setStep] = useState(1);
    const [customerDetails, setCustomerDetails] = useState({
        name: '',
        email: '',
        phone: ''
    });

    const handleSeatClick = (seat, category) => {
        const seatId = seat.id;
        const isSelected = selectedSeats.find(s => s.id === seatId);

        if (isSelected) {
            setSelectedSeats(selectedSeats.filter(s => s.id !== seatId));
        } else {
            setSelectedSeats([...selectedSeats, { ...seat, category, price: category.price }]);
        }
    };

    const handleGAQuantityChange = (areaId, quantity) => {
        if (quantity === 0) {
            const newGA = { ...selectedGA };
            delete newGA[areaId];
            setSelectedGA(newGA);
        } else {
            setSelectedGA({
                ...selectedGA,
                [areaId]: quantity
            });
        }
    };

    const calculateTotal = () => {
        let total = 0;

        selectedSeats.forEach(seat => {
            total += seat.price;
        });

        Object.entries(selectedGA).forEach(([areaId, quantity]) => {
            const area = seatingOptions.general_admission.find(a => a.id.toString() === areaId);
            if (area) {
                total += area.price * quantity;
            }
        });

        return total;
    };

    const getTotalTickets = () => {
        const seatCount = selectedSeats.length;
        const gaCount = Object.values(selectedGA).reduce((sum, qty) => sum + qty, 0);
        return seatCount + gaCount;
    };

    const handleProceedToCheckout = async () => {
        if (getTotalTickets() === 0) {
            alert('Please select at least one ticket');
            return;
        }

        setLoading(true);

        // Prepare ticket requests
        const ticketRequests = [];

        selectedSeats.forEach(seat => {
            ticketRequests.push({
                type: 'assigned_seat',
                seat_id: seat.id,
                ticket_type_id: null
            });
        });

        Object.entries(selectedGA).forEach(([areaId, quantity]) => {
            ticketRequests.push({
                type: 'general_admission',
                area_id: parseInt(areaId),
                quantity: quantity
            });
        });

        // Use Inertia router to make POST request
        router.post(`/api/shows/${show.id}/hold-tickets`, {
            tickets: ticketRequests
        }, {
            onSuccess: (response) => {
                // Navigate to checkout page
                router.visit('/booking/checkout', {
                    method: 'get',
                    data: { holds: response.data.holds.map(h => h.id).join(',') }
                });
            },
            onError: (error) => {
                console.error('Error holding tickets:', error);
                alert('Error reserving tickets. Please try again.');
            },
            onFinish: () => {
                setLoading(false);
            }
        });
    };

return (
        <>
            <Head title={`Book Tickets - ${show.title}`} />
            <div className="min-h-screen bg-gray-50 py-8">
                {/* Your existing JSX */}
                <div className="max-w-6xl mx-auto px-4">
                    {/* Header */}
                    <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <div className="flex justify-between items-start">
                            <div>
                                <h1 className="text-2xl font-bold text-gray-900 mb-2">
                                    {show.title}
                                </h1>
                                <div className="flex items-center text-gray-600 space-x-4">
                                    <span className="flex items-center">
                                        <Clock className="w-4 h-4 mr-1" />
                                        {new Date(show.start_date).toLocaleDateString()}
                                    </span>
                                    <span className="flex items-center">
                                        <MapPin className="w-4 h-4 mr-1" />
                                        {show.venue.name}
                                    </span>
                                </div>
                            </div>
                            <div className="text-right">
                                <div className="text-lg font-semibold">
                                    Total: ${calculateTotal()}
                                </div>
                                <div className="text-sm text-gray-600">
                                    {getTotalTickets()} ticket{getTotalTickets() !== 1 ? 's' : ''}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Rest of your component JSX */}
                    {/* ... */}

                    <div className="flex justify-center">
                        <button
                            onClick={handleProceedToCheckout}
                            disabled={getTotalTickets() === 0 || loading}
                            className="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {loading ? 'Processing...' : `Proceed to Checkout (${getTotalTickets()} ticket${getTotalTickets() !== 1 ? 's' : ''})`}
                        </button>
                    </div>
                </div>
            </div>
        </>
    );
};

export default SeatSelection;
