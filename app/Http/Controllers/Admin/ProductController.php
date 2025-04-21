<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('categories')->latest()->paginate(10);
        
        return Inertia::render('Admin/Products/Index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        
        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'featured' => 'boolean',
            'active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'available_sizes' => 'nullable|json',
            'available_colors' => 'nullable|json',
            'specifications' => 'nullable|json',
        ]);
        
        // Handle image uploads
        $imagesPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagesPaths[] = $path;
            }
        }
        
        // Create product
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'sku' => $validated['sku'],
            'images' => $imagesPaths,
            'featured' => $validated['featured'] ?? false,
            'active' => $validated['active'] ?? true,
            'available_sizes' => json_decode($validated['available_sizes'] ?? '[]'),
            'available_colors' => json_decode($validated['available_colors'] ?? '[]'),
            'specifications' => json_decode($validated['specifications'] ?? '{}'),
        ]);
        
        // Attach categories
        $product->categories()->attach($validated['category_ids']);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load('categories');
        $categories = Category::all();
        
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'selectedCategoryIds' => $product->categories->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'featured' => 'boolean',
            'active' => 'boolean',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'remove_images' => 'nullable|array',
            'available_sizes' => 'nullable|json',
            'available_colors' => 'nullable|json',
            'specifications' => 'nullable|json',
        ]);
        
        // Handle image uploads (keep existing logic)
        $currentImages = $product->images ?? [];
        
        // Remove images if requested
        if (isset($validated['remove_images']) && is_array($validated['remove_images'])) {
            foreach ($validated['remove_images'] as $imagePath) {
                if (in_array($imagePath, $currentImages)) {
                    Storage::disk('public')->delete($imagePath);
                    $currentImages = array_diff($currentImages, [$imagePath]);
                }
            }
        }
        
        // Add new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('products', 'public');
                $currentImages[] = $path;
            }
        }
        
        // Update product
        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'sku' => $validated['sku'],
            'images' => array_values($currentImages), // Re-index array
            'featured' => $validated['featured'] ?? false,
            'active' => $validated['active'] ?? true,
            'available_sizes' => json_decode($validated['available_sizes'] ?? '[]'),
            'available_colors' => json_decode($validated['available_colors'] ?? '[]'),
            'specifications' => json_decode($validated['specifications'] ?? '{}'),
        ]);
        
        // Sync categories
        $product->categories()->sync($validated['category_ids']);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete product images
        if (!empty($product->images)) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        // Delete product
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}