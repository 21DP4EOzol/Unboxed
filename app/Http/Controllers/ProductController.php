<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display the product listing.
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search = $request->query('search');
        $sort = $request->query('sort', 'newest');
        
        $query = Product::with('categories')->where('active', true);
        
        // Filter by category if specified
        if ($category) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }
        
        // Search products
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Sort products
        switch ($sort) {
            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }
        
        $products = $query->paginate(12)->withQueryString();
        
        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => [
                'category' => $category,
                'search' => $search,
                'sort' => $sort
            ]
        ]);
    }
    
    /**
     * Display the product details.
     */
    public function show(Product $product)
    {
        if (!$product->active) {
            abort(404);
        }
        
        $product->load('categories');
        
        // Get related products
        $relatedProducts = Product::with('categories')
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->take(4)
            ->get();
        
        return Inertia::render('Products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}