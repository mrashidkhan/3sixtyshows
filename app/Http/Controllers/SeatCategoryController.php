<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatCategory;
use Illuminate\Http\Request;

class SeatCategoryController extends Controller
{
    // Standard CRUD methods (index, create, store, edit, update, destroy)

    public function index()
    {
        $seatCategories = SeatCategory::orderBy('display_order')->get();
        return view('admin.seats.categories.index', compact('seatCategories'));
    }

    public function create()
    {
        return view('admin.seats.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color_code' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        SeatCategory::create($validated);

        return redirect()->route('admin.seat-categories.index')
            ->with('success', 'Seat category created successfully.');
    }

    // Edit, update, and destroy methods follow similar patterns
}
