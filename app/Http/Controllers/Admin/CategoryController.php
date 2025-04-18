<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShowCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Add your admin middleware here
    }

    public function index()
    {
        $categories = ShowCategory::withCount('shows')
                                 ->orderBy('name')
                                 ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:show_categories',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:webp,jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Store original image
            $path = $image->storeAs('public/categories', $filename);

            // Create thumbnail
            $thumbnail = Image::make($image)
                ->resize(400, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode();

            Storage::put('public/categories/thumbnails/' . $filename, $thumbnail);

            $validated['image'] = $filename;
        }

        // Create category
        $category = ShowCategory::create($validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category created successfully!');
    }

    public function show($id)
    {
        $category = ShowCategory::withCount('shows')->findOrFail($id);

        // Get shows in this category
        $shows = $category->shows()
                         ->with('venue')
                         ->orderBy('start_date', 'desc')
                         ->paginate(10);

        return view('admin.categories.show', compact('category', 'shows'));
    }

    public function edit($id)
    {
        $category = ShowCategory::findOrFail($id);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = ShowCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:show_categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle image upload if present
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::delete([
                    'public/categories/' . $category->image,
                    'public/categories/thumbnails/' . $category->image
                ]);
            }

            // Upload new image
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Store original image
            $path = $image->storeAs('public/categories', $filename);

            // Create thumbnail
            $thumbnail = Image::make($image)
                ->resize(400, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode();

            Storage::put('public/categories/thumbnails/' . $filename, $thumbnail);

            $validated['image'] = $filename;
        }

        // Update category
        $category->update($validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = ShowCategory::withCount('shows')->findOrFail($id);

        // Check if category has shows
        if ($category->shows_count > 0) {
            return redirect()->route('admin.categories.index')
                            ->with('error', 'Cannot delete category because it has associated shows. Please reassign or delete the shows first.');
        }

        // Delete images
        if ($category->image) {
            Storage::delete([
                'public/categories/' . $category->image,
                'public/categories/thumbnails/' . $category->image
            ]);
        }

        // Delete category
        $category->delete();

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category deleted successfully!');
    }

    public function toggleActive($id)
    {
        $category = ShowCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return redirect()->back()
                        ->with('success', 'Category status updated successfully!');
    }
}
