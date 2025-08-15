<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Show;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all ticket types across all shows
     */
    public function all(Request $request)
    {
        try {
            $query = TicketType::with(['show.venue']);

            // Apply filters
            if ($request->filled('show')) {
                $query->where('show_id', $request->show);
            }

            if ($request->filled('status')) {
                $query->where('is_active', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('show', function($showQuery) use ($search) {
                          $showQuery->where('title', 'like', "%{$search}%");
                      });
                });
            }

            // Order by show date and ticket type name
            $ticketTypes = $query->join('shows', 'ticket_types.show_id', '=', 'shows.id')
                                ->orderBy('shows.start_date', 'desc')
                                ->orderBy('ticket_types.display_order', 'asc')
                                ->orderBy('ticket_types.name', 'asc')
                                ->select('ticket_types.*')
                                ->paginate(20);

            // Get all shows for filter dropdown
            $shows = Show::orderBy('start_date', 'desc')->get(['id', 'title', 'start_date']);

            return view('admin.ticket-types.all', compact('ticketTypes', 'shows'));

        } catch (\Exception $e) {
            Log::error('Error in TicketTypeController@all: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load ticket types.');
        }
    }

    /**
     * Display ticket types for a specific show
     */
    public function index(Show $show)
    {
        try {
            $ticketTypes = $show->ticketTypes()
                               ->orderBy('display_order', 'asc')
                               ->orderBy('name', 'asc')
                               ->get();

            return view('admin.ticket-types.index', compact('show', 'ticketTypes'));

        } catch (\Exception $e) {
            Log::error('Error in TicketTypeController@index: ' . $e->getMessage());
            return redirect()->route('show.index')->with('error', 'Failed to load ticket types for this show.');
        }
    }






    /**
     * Show the form for editing the specified ticket type
     */
    public function edit(TicketType $ticketType)
    {
        try {
            $show = $ticketType->show;

            if (!$show) {
                return redirect()->route('admin.ticket-types.all')
                               ->with('error', 'Show not found for this ticket type.');
            }

            return view('admin.ticket-types.edit', compact('ticketType', 'show'));

        } catch (\Exception $e) {
            Log::error('Error in TicketTypeController@edit: ' . $e->getMessage());
            return redirect()->route('admin.ticket-types.all')
                           ->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified ticket type
     */
    public function update(Request $request, TicketType $ticketType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        // Check if reducing capacity below sold tickets
        $soldTickets = $ticketType->tickets()->count();
        if ($validated['capacity'] && $validated['capacity'] < $soldTickets) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Cannot set capacity to {$validated['capacity']} because {$soldTickets} tickets have already been sold.");
        }

        DB::beginTransaction();

        try {
            $oldData = $ticketType->toArray();

            $ticketType->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'capacity' => $validated['capacity'] ?? null,
                'display_order' => $validated['display_order'] ?? 0,
                'is_active' => $validated['is_active'],
            ]);

            DB::commit();

            Log::info('Ticket type updated successfully', [
                'ticket_type_id' => $ticketType->id,
                'updated_by' => auth()->id(),
                'old_data' => $oldData,
                'new_data' => $validated
            ]);

            return redirect()
                ->route('admin.ticket-types.index', $ticketType->show)
                ->with('success', 'Ticket type "' . $ticketType->name . '" updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating ticket type: ' . $e->getMessage(), [
                'ticket_type_id' => $ticketType->id,
                'data' => $validated
            ]);

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
        $ticketTypeName = $ticketType->name;
        $ticketTypeId = $ticketType->id;

        // Check if there are any tickets sold for this ticket type
        $soldTicketsCount = $ticketType->tickets()->count();
        if ($soldTicketsCount > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete ticket type "' . $ticketTypeName . '" because ' . $soldTicketsCount . ' tickets have already been sold.');
        }

        DB::beginTransaction();

        try {
            $ticketType->delete();

            DB::commit();

            Log::info('Ticket type deleted successfully', [
                'ticket_type_id' => $ticketTypeId,
                'ticket_type_name' => $ticketTypeName,
                'show_id' => $show->id,
                'deleted_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.ticket-types.index', $show)
                ->with('success', 'Ticket type "' . $ticketTypeName . '" deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting ticket type: ' . $e->getMessage(), [
                'ticket_type_id' => $ticketTypeId
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Toggle ticket type status (AJAX)
     */
    public function toggleStatus(TicketType $ticketType)
    {
        try {
            $oldStatus = $ticketType->is_active;
            $newStatus = !$oldStatus;

            $ticketType->update(['is_active' => $newStatus]);

            Log::info('Ticket type status toggled', [
                'ticket_type_id' => $ticketType->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'toggled_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'is_active' => $ticketType->is_active,
                'message' => 'Ticket type status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling ticket type status: ' . $e->getMessage(), [
                'ticket_type_id' => $ticketType->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ticket type statistics (AJAX)
     */
    public function getStats(TicketType $ticketType)
    {
        try {
            $soldTickets = $ticketType->tickets()->count();
            $availableTickets = $ticketType->capacity ? max(0, $ticketType->capacity - $soldTickets) : null;
            $totalRevenue = $ticketType->tickets()->sum('price') ?? 0;

            $stats = [
                'total_capacity' => $ticketType->capacity,
                'sold_tickets' => $soldTickets,
                'available_tickets' => $availableTickets,
                'is_sold_out' => $ticketType->capacity ? ($soldTickets >= $ticketType->capacity) : false,
                'total_revenue' => $totalRevenue,
                'formatted_revenue' => '$' . number_format($totalRevenue, 2),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Error getting ticket type stats: ' . $e->getMessage(), [
                'ticket_type_id' => $ticketType->id
            ]);

            return response()->json([
                'error' => 'Failed to get statistics'
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
            $duplicatedCount = 0;

            foreach ($sourceTicketTypes as $sourceTicketType) {
                $show->ticketTypes()->create([
                    'name' => $sourceTicketType->name,
                    'description' => $sourceTicketType->description,
                    'price' => $sourceTicketType->price,
                    'capacity' => $sourceTicketType->capacity,
                    'display_order' => $sourceTicketType->display_order,
                    'is_active' => $sourceTicketType->is_active,
                ]);
                $duplicatedCount++;
            }

            DB::commit();

            Log::info('Ticket types duplicated successfully', [
                'source_show_id' => $sourceShow->id,
                'target_show_id' => $show->id,
                'duplicated_count' => $duplicatedCount,
                'duplicated_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.ticket-types.index', $show)
                ->with('success', "Successfully duplicated {$duplicatedCount} ticket types from \"{$sourceShow->title}\"!");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error duplicating ticket types: ' . $e->getMessage(), [
                'source_show_id' => $request->source_show_id,
                'target_show_id' => $show->id
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to duplicate ticket types: ' . $e->getMessage());
        }
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
            'ticket_types.*.capacity' => 'nullable|integer|min:1',
            'ticket_types.*.is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $updatedCount = 0;

            foreach ($request->ticket_types as $ticketTypeData) {
                $ticketType = TicketType::find($ticketTypeData['id']);

                if ($ticketType && $ticketType->show_id === $show->id) {
                    // Check capacity constraint
                    $soldTickets = $ticketType->tickets()->count();
                    if (isset($ticketTypeData['capacity']) && $ticketTypeData['capacity'] < $soldTickets) {
                        throw new \Exception("Cannot set capacity for \"{$ticketType->name}\" to {$ticketTypeData['capacity']} because {$soldTickets} tickets have already been sold.");
                    }

                    $ticketType->update([
                        'price' => $ticketTypeData['price'],
                        'capacity' => $ticketTypeData['capacity'] ?? null,
                        'is_active' => $ticketTypeData['is_active'] ?? false,
                    ]);

                    $updatedCount++;
                }
            }

            DB::commit();

            Log::info('Bulk ticket types update completed', [
                'show_id' => $show->id,
                'updated_count' => $updatedCount,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} ticket types"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in bulk update: ' . $e->getMessage(), [
                'show_id' => $show->id,
                'data' => $request->ticket_types
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export ticket types to CSV
     */
    public function exportCsv(Show $show)
    {
        try {
            $ticketTypes = $show->ticketTypes()->with('tickets')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ticket-types-' . $show->slug . '-' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($ticketTypes) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Name',
                    'Description',
                    'Price',
                    'Capacity',
                    'Sold Tickets',
                    'Available Tickets',
                    'Status',
                    'Display Order',
                    'Created At'
                ]);

                // Add data rows
                foreach ($ticketTypes as $ticketType) {
                    $soldTickets = $ticketType->tickets->count();
                    $availableTickets = $ticketType->capacity ? ($ticketType->capacity - $soldTickets) : 'Unlimited';

                    fputcsv($file, [
                        $ticketType->id,
                        $ticketType->name,
                        $ticketType->description,
                        $ticketType->price,
                        $ticketType->capacity ?? 'Unlimited',
                        $soldTickets,
                        $availableTickets,
                        $ticketType->is_active ? 'Active' : 'Inactive',
                        $ticketType->display_order,
                        $ticketType->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting ticket types: ' . $e->getMessage(), [
                'show_id' => $show->id
            ]);

            return redirect()->back()->with('error', 'Failed to export ticket types.');
        }
    }


/**
 * Show the form for creating a new ticket type (NEW - without show parameter)
 */
public function create()
{
    try {
        // Get all active shows for selection
        $shows = Show::where('is_active', true)
                    ->orderBy('start_date', 'desc')
                    ->get(['id', 'title', 'start_date', 'venue_id'])
                    ->load('venue:id,name');

        return view('admin.ticket-types.create', compact('shows'));

    } catch (\Exception $e) {
        Log::error('Error in TicketTypeController@create: ' . $e->getMessage());
        return redirect()->route('admin.ticket-types.all')
                       ->with('error', 'Failed to load create form.');
    }
}

/**
 * Store a newly created ticket type (NEW - with show selection)
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'show_id' => 'required|exists:shows,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'price' => 'required|numeric|min:0',
        'capacity' => 'nullable|integer|min:1',
        'display_order' => 'nullable|integer|min:0',
        'is_active' => 'nullable|boolean',
    ]);

    DB::beginTransaction();

    try {
        $show = Show::findOrFail($validated['show_id']);

        $ticketType = $show->ticketTypes()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'capacity' => $validated['capacity'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        DB::commit();

        Log::info('Ticket type created successfully', [
            'ticket_type_id' => $ticketType->id,
            'show_id' => $show->id,
            'created_by' => auth()->id()
        ]);

        return redirect()
            ->route('admin.ticket-types.index', $show)
            ->with('success', 'Ticket type "' . $ticketType->name . '" created successfully for "' . $show->title . '"!');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error creating ticket type: ' . $e->getMessage(), [
            'data' => $validated
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to create ticket type: ' . $e->getMessage());
    }
}

/**
 * AJAX method to search shows for select2
 */
public function searchShows(Request $request)
{
    try {
        $search = $request->get('q', '');

        $shows = Show::where('is_active', true)
                    ->where(function($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                              ->orWhereHas('venue', function($venueQuery) use ($search) {
                                  $venueQuery->where('name', 'like', "%{$search}%");
                              });
                    })
                    ->with('venue:id,name')
                    ->orderBy('start_date', 'desc')
                    ->limit(20)
                    ->get(['id', 'title', 'start_date', 'venue_id']);

        $results = $shows->map(function($show) {
            return [
                'id' => $show->id,
                'text' => $show->title . ' - ' . ($show->venue->name ?? 'No Venue') . ' (' . $show->start_date->format('M d, Y') . ')',
                'title' => $show->title,
                'venue' => $show->venue->name ?? 'No Venue',
                'date' => $show->start_date->format('M d, Y H:i')
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);

    } catch (\Exception $e) {
        Log::error('Error searching shows: ' . $e->getMessage());
        return response()->json(['results' => [], 'pagination' => ['more' => false]]);
    }
}

/**
 * Show the form for creating a new ticket type for specific show (BACKWARD COMPATIBILITY)
 */
public function createForShow(Show $show)
{
    try {
        return view('admin.ticket-types.create-for-show', compact('show'));

    } catch (\Exception $e) {
        Log::error('Error in TicketTypeController@createForShow: ' . $e->getMessage());
        return redirect()->route('admin.ticket-types.index', $show)
                       ->with('error', 'Failed to load create form.');
    }
}

/**
 * Store a newly created ticket type for specific show (BACKWARD COMPATIBILITY)
 */
public function storeForShow(Request $request, Show $show)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'price' => 'required|numeric|min:0',
        'capacity' => 'nullable|integer|min:1',
        'display_order' => 'nullable|integer|min:0',
        'is_active' => 'nullable|boolean',
    ]);

    DB::beginTransaction();

    try {
        $ticketType = $show->ticketTypes()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'capacity' => $validated['capacity'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        DB::commit();

        Log::info('Ticket type created for show successfully', [
            'ticket_type_id' => $ticketType->id,
            'show_id' => $show->id,
            'created_by' => auth()->id()
        ]);

        return redirect()
            ->route('admin.ticket-types.index', $show)
            ->with('success', 'Ticket type "' . $ticketType->name . '" created successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error creating ticket type for show: ' . $e->getMessage(), [
            'show_id' => $show->id,
            'data' => $validated
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to create ticket type: ' . $e->getMessage());
    }
}
}
