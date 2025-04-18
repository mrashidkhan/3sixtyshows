<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; // Add this import
use App\Models\ShowCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $categories=Category::where('status','inactive')->get();
        $showCategories=ShowCategory::all();
        return view('admin.showcategory.index',compact('showCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories=ShowCategory::whereNull('id')->get();
        return view('admin.showcategory.add',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:show_categories,slug',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'is_active' => 'required|in:0,1', // Changed to match the form values
        'category_id' => 'nullable|exists:categories,id',
    ]);

    // Handle the image upload if provided
$imagePath = null;
if ($request->hasFile('image') && $request->file('image')->isValid()) {
    try {
        // Get filename with extension
        $filenameWithExt = $request->file('image')->getClientOriginalName();

        // Get just filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        // Get just extension
        $extension = $request->file('image')->getClientOriginalExtension();

        // Filename to store - add timestamp to prevent duplicates
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        // Store the file
        $imagePath = $request->file('image')->storeAs('show-categories', $fileNameToStore, 'public');

        // For debugging
        \Log::info('Image uploaded: ' . $imagePath);
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Image upload failed: ' . $e->getMessage());
        return back()->with('error', 'Image upload failed: ' . $e->getMessage());
    }
}

    // Create a new show category using the validated data
    $showCategory = ShowCategory::create([
        'name' => $request->input('name'),
        'slug' => $request->input('slug'),
        'description' => $request->input('description'),
        'image' => $imagePath,
        'is_active' => $request->input('is_active'), // This already contains 0 or 1
        'category_id' => $request->input('category_id'),
    ]);

    // Flash a success message to the session
    session()->flash('success', 'Show category created successfully.');

    // Redirect to the show category list route
    return redirect()->route('showcategory.list');
}

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($id)
    // {
    //     // Retrieve the category by ID or fail if not found
    //     $category = ShowCategory::findOrFail($id);

    //     // Retrieve all categories for the subcategory dropdown
    //     $categories = ShowCategory::all();

    //     // Return the edit view with the category and all categories
    //     return view('admin.showcategory.edit', compact('category', 'categories'));
    // }

    public function edit($id)
{
    $showCategory = ShowCategory::findOrFail($id);
    return view('admin.showcategory.edit', compact('showCategory'));
}



    public function update(Request $request, $id)
{
    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255', // Required field
        'description' => 'nullable|string', // Optional field
        'is_active' => 'required|in:1,0', // Assuming status can be 'active' or 'inactive'
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Find the category by ID
    $category = ShowCategory::findOrFail($id);

    // Handle the image upload if provided
$imagePath = null;
if ($request->hasFile('image') && $request->file('image')->isValid()) {
    try {
        // Get filename with extension
        $filenameWithExt = $request->file('image')->getClientOriginalName();

        // Get just filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        // Get just extension
        $extension = $request->file('image')->getClientOriginalExtension();

        // Filename to store - add timestamp to prevent duplicates
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        // Store the file
        $imagePath = $request->file('image')->storeAs('show-categories', $fileNameToStore, 'public');

        // Assign the image path to the category's image field
        $category->image = $imagePath;

        // For debugging
        \Log::info('Image uploaded: ' . $imagePath);
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Image upload failed: ' . $e->getMessage());
        return back()->with('error', 'Image upload failed: ' . $e->getMessage());
    }
}

    // Update the category with the validated data
    $category->name = $request->input('name');
    $category->description = $request->input('description');
    $category->is_active = $request->input('is_active');
    // No need to set $category->id since it's already the same as $id
    $category->slug = $request->input('slug');
    // $category->image = $request->input('image');

    // Save the changes to the database
    $category->save();

    // Redirect or return a response
    return redirect()->route('showcategory.list')->with('success', 'Show Category updated successfully.');
    // return response()->json(['filename' => $imagePath, 'success' => true]);
}

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Category $category)
    // {
    //     $id=$request->id;
    //     $data=array(
    //         'name'=>$request->name,
    //         'category_id'=>$request->category_id,
    //     );
    //     $category=Category::find($id);
    //     $category->update($data);
    //     return redirect()->route('category.list');
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)  // Changed to match the route parameter
{
    $category = ShowCategory::find($id);

    if ($category) {
        $category->delete();
        return redirect()->route('showcategory.list')->with('success', 'Show Category deleted successfully.');
    } else {
        return redirect()->route('showcategory.list')->with('failer', 'Show Category is not found.');
    }
}

}
