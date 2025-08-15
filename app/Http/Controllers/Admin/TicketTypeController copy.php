<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Show;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display ticket types for a specific show
     */
    public function index(Show $show)
    {
        $ticketTypes = $show->ticketTypes()->orderBy('name')->get();

        return view('admin.ticket-types.index', compact('show', 'ticketTypes'));
    }

    /**
     * Show the form for creating a new ticket type
     */
    public function create(Show $show)
    {
        return view('admin.ticket-types.create', compact('show'));
    }

    /**
     * Store a newly created ticket type
     */
    public function store(Request $request, Show $show)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'max_per_booking' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
        ]);

        DB::beginTransaction();

        try {
            $ticketType = $show->ticketTypes()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'max_per_booking' => $validated['max_per_booking'] ?? null,
                'is_active' => $request->has('is_active'),
                'sale_start_date' => $validated['sale_start_date'] ?? null,
                'sale_end_date' => $validated['sale_end_date'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.ticket-types.index', $show)
                ->with('success', 'Ticket type created successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified ticket type
     */
    public function edit(TicketType $ticketType)
    {
        $show = $ticketType->show;

        return view('admin.ticket-types.edit', compact('ticketType', 'show'));
    }

    /**
     * Update the specified ticket type
     */
    public function update(Request $request, TicketType $ticketType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'max_per_booking' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
        ]);

        DB::beginTransaction();

        try {
            $ticketType->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'max_per_booking' => $validated['max_per_booking'] ?? null,
                'is_active' => $request->has('is_active'),
                'sale_start_date' => $validated['sale_start_date'] ?? null,
                'sale_end_date' => $validated['sale_end_date'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.ticket-types.index', $ticketType->show)
                ->with('success', 'Ticket type updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ticket type
     */
    public function destroy(TicketType $ticketType)
    {
        $show = $ticketType->show;

        // Check if there are any bookings using this ticket type
        if ($ticketType->bookingItems()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete ticket type that has existing bookings.');
        }

        DB::beginTransaction();

        try {
            $ticketType->delete();

            DB::commit();

            return redirect()
                ->route('admin.ticket-types.index', $show)
                ->with('success', 'Ticket type deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Toggle ticket type status
     */
    public function toggleStatus(TicketType $ticketType)
    {
        $ticketType->update(['is_active' => !$ticketType->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $ticketType->is_active,
            'message' => 'Ticket type status updated successfully'
        ]);
    }

    /**
     * Get ticket type statistics
     */
    public function getStats(TicketType $ticketType)
    {
        $stats = [
            'total_quantity' => $ticketType->quantity,
            'sold_quantity' => $ticketType->bookingItems()->sum('quantity'),
            'available_quantity' => $ticketType->quantity - $ticketType->bookingItems()->sum('quantity'),
            'total_revenue' => $ticketType->bookingItems()->sum(DB::raw('quantity * price')),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update ticket types
     */
    public function bulkUpdate(Request $request, Show $show)
    {
        $request->validate([
            'ticket_types' => 'required|array',
            'ticket_types.*.id' => 'required|exists:ticket_types,id',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.quantity' => 'required|integer|min:1',
            'ticket_types.*.is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->ticket_types as $ticketTypeData) {
                $ticketType = TicketType::find($ticketTypeData['id']);

                if ($ticketType && $ticketType->show_id === $show->id) {
                    $ticketType->update([
                        'price' => $ticketTypeData['price'],
                        'quantity' => $ticketTypeData['quantity'],
                        'is_active' => $ticketTypeData['is_active'] ?? false,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket types updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate ticket types from another show
     */
    public function duplicate(Request $request, Show $show)
    {
        $request->validate([
            'source_show_id' => 'required|exists:shows,id'
        ]);

        $sourceShow = Show::find($request->source_show_id);
        $sourceTicketTypes = $sourceShow->ticketTypes;

        if ($sourceTicketTypes->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No ticket types found in the selected show.');
        }

        DB::beginTransaction();

        try {
            foreach ($sourceTicketTypes as $sourceTicketType) {
                $show->ticketTypes()->create([
                    'name' => $sourceTicketType->name,
                    'description' => $sourceTicketType->description,
                    'price' => $sourceTicketType->price,
                    'quantity' => $sourceTicketType->quantity,
                    'max_per_booking' => $sourceTicketType->max_per_booking,
                    'is_active' => $sourceTicketType->is_active,
                    'sale_start_date' => $sourceTicketType->sale_start_date,
                    'sale_end_date' => $sourceTicketType->sale_end_date,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.ticket-types.index', $show)
                ->with('success', 'Ticket types duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->with('error', 'Failed to duplicate ticket types: ' . $e->getMessage());
        }
    }
}
