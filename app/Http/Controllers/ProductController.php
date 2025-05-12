<?php
// Written & debugged by: Tech Ngoun Leang & Ratanakvesal Thong
// Tested by: Tech Ngoun Leang
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Display a listing of the products
    public function index()
    {
        $products = Product::with('category')->get();  // Eager load category to reduce queries
        return view('products.index', compact('products'));
    }

    // Show the form for creating a new product
    public function create()
    {
        $categories = Category::all();  // Fetch all categories to populate the dropdown
        return view('products.create', compact('categories'));
    }

    // Store a newly created product in the database
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer',
            'reorder_level' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // still validate as 'image'
        ]);

        // Handle the image upload
        $photoPath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $photoPath = $request->file('image')->store('product_images', 'public');
        }

        // Create the product
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock_quantity' => $request->stock_quantity,
            'reorder_level' => $request->reorder_level,
            'photo_path' => $photoPath,  // Use photo_path field
        ]);

        return redirect()->route('products.index')->with('success', __('app.product_created_successfully'));
    }

    // Display the specified product
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    // Show the form for editing the specified product
    public function edit(Product $product)
    {
        $categories = Category::all();  // Fetch all categories for the dropdown
        return view('products.edit', compact('product', 'categories'));
    }

    // Update the specified product in the database
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer',
            'reorder_level' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->except('image');

        // Handle image update
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete the old image if it exists
            if ($product->photo_path && Storage::disk('public')->exists($product->photo_path)) {
                Storage::disk('public')->delete($product->photo_path);
            }

            $data['photo_path'] = $request->file('image')->store('product_images', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Remove the specified product from the database
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // adjust size as needed
        ]);

        // Handle the uploaded file
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store the image in the 'public' disk, inside 'images' folder
            $path = $request->file('image')->store('images', 'public');

            // You can save the file path to the database if needed
            // Example: User::create(['image_path' => $path]);

            return back()->with('success', 'Image uploaded successfully!')->with('path', $path);
        }

        return back()->with('error', 'Image upload failed!');
    }
}
