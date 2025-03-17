<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Swipe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SwipeController extends Controller
{
    public function index()
    {
        // Get a list of products the user hasn't swiped on yet
        $user = auth()->user();
        
        $swipedProductIds = $user->swipes()->pluck('product_id')->toArray();
        
        $products = Product::with('categories')
            ->whereNotIn('id', $swipedProductIds)
            ->where('active', true)
            ->inRandomOrder()
            ->take(10)
            ->get();
        
        return Inertia::render('Swipe/Index', [
            'products' => $products
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'direction' => 'required|in:left,right',
        ]);
        
        // Check if user already swiped on this product
        $existingSwipe = Swipe::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->first();
            
        if ($existingSwipe) {
            return response()->json([
                'message' => 'You have already swiped on this product.'
            ], 422);
        }
        
        // Create the swipe
        $swipe = Swipe::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
            'direction' => $validated['direction'],
        ]);
        
        return response()->json([
            'message' => 'Swipe recorded successfully.',
            'swipe' => $swipe,
        ]);
    }
    
    public function history()
    {
        $user = auth()->user();
        
        $likedProducts = $user->swipes()
            ->with('product', 'product.categories')
            ->where('direction', 'right')
            ->latest()
            ->get()
            ->pluck('product');
            
        $dislikedProducts = $user->swipes()
            ->with('product', 'product.categories')
            ->where('direction', 'left')
            ->latest()
            ->get()
            ->pluck('product');
        
        return Inertia::render('Swipe/History', [
            'likedProducts' => $likedProducts,
            'dislikedProducts' => $dislikedProducts,
        ]);
    }
}