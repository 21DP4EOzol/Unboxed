<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Swipe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SwipeController extends Controller
{
    public function index(Request $request)
    {
        // Get the swipe type from the request, default to product
        $swipeType = $request->query('type', 'product');
        
        // Get a list of items the user hasn't swiped on yet
        $user = auth()->user();
        
        if ($swipeType === 'product') {
            // Get products the user hasn't swiped on
            $swipedProductIds = $user->swipes()->whereNotNull('product_id')->pluck('product_id')->toArray();
            
            $items = Product::with('categories')
                ->whereNotIn('id', $swipedProductIds)
                ->where('active', true)
                ->inRandomOrder()
                ->take(10)
                ->get();
                
            $itemType = 'product';
        } else {
            // Get categories the user hasn't swiped on
            $swipedCategoryIds = $user->swipes()->whereNotNull('category_id')->pluck('category_id')->toArray();
            
            $items = Category::where('active', true)
                ->whereNotIn('id', $swipedCategoryIds)
                ->inRandomOrder()
                ->take(10)
                ->get();
                
            $itemType = 'category';
        }
        
        return Inertia::render('Swipe/Index', [
            'items' => $items,
            'itemType' => $itemType
        ]);
    }
    
    public function store(Request $request)
    {
        // Validate based on the type
        if ($request->type === 'product') {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'direction' => 'required|in:left,right',
                'type' => 'required|in:product,category',
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
                'category_id' => null,
                'direction' => $validated['direction'],
                'type' => 'product',
            ]);
        } else {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'direction' => 'required|in:left,right',
                'type' => 'required|in:product,category',
            ]);
            
            // Check if user already swiped on this category
            $existingSwipe = Swipe::where('user_id', auth()->id())
                ->where('category_id', $validated['category_id'])
                ->first();
                
            if ($existingSwipe) {
                return response()->json([
                    'message' => 'You have already swiped on this category.'
                ], 422);
            }
            
            // Create the swipe
            $swipe = Swipe::create([
                'user_id' => auth()->id(),
                'product_id' => null,
                'category_id' => $validated['category_id'],
                'direction' => $validated['direction'],
                'type' => 'category',
            ]);
        }
        
        return response()->json([
            'message' => 'Swipe recorded successfully.',
            'swipe' => $swipe,
        ]);
    }
    
    public function history()
    {
        $user = auth()->user();
        
        // Get liked products
        $likedProducts = $user->swipes()
            ->with('product', 'product.categories')
            ->where('direction', 'right')
            ->whereNotNull('product_id')
            ->where('type', 'product')
            ->latest()
            ->get()
            ->pluck('product');
            
        // Get disliked products
        $dislikedProducts = $user->swipes()
            ->with('product', 'product.categories')
            ->where('direction', 'left')
            ->whereNotNull('product_id')
            ->where('type', 'product')
            ->latest()
            ->get()
            ->pluck('product');
            
        // Get liked categories
        $likedCategories = $user->swipes()
            ->with('category')
            ->where('direction', 'right')
            ->whereNotNull('category_id')
            ->where('type', 'category')
            ->latest()
            ->get()
            ->pluck('category');
            
        // Get disliked categories
        $dislikedCategories = $user->swipes()
            ->with('category')
            ->where('direction', 'left')
            ->whereNotNull('category_id')
            ->where('type', 'category')
            ->latest()
            ->get()
            ->pluck('category');
        
        return Inertia::render('Swipe/History', [
            'likedProducts' => $likedProducts,
            'dislikedProducts' => $dislikedProducts,
            'likedCategories' => $likedCategories,
            'dislikedCategories' => $dislikedCategories,
        ]);
    }

    /**
     * Remove selected products or categories from swipe history
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeSelected(Request $request)
    {
        $validated = $request->validate([
            'productIds' => 'array',
            'productIds.*' => 'exists:products,id',
            'categoryIds' => 'array',
            'categoryIds.*' => 'exists:categories,id',
        ]);

        // Delete swipes for the selected products
        if (!empty($validated['productIds'])) {
            Swipe::where('user_id', auth()->id())
                ->whereNotNull('product_id')
                ->whereIn('product_id', $validated['productIds'])
                ->delete();
        }
        
        // Delete swipes for the selected categories
        if (!empty($validated['categoryIds'])) {
            Swipe::where('user_id', auth()->id())
                ->whereNotNull('category_id')
                ->whereIn('category_id', $validated['categoryIds'])
                ->delete();
        }
        
        $total = (isset($validated['productIds']) ? count($validated['productIds']) : 0) + 
                (isset($validated['categoryIds']) ? count($validated['categoryIds']) : 0);

        return redirect()->route('swipe.history')
            ->with('success', $total . ' items removed from your swipe history and added back to discovery.');
    }
}