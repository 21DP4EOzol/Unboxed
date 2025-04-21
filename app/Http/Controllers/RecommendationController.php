<?php

namespace App\Http\Controllers;

use App\Services\Recommendation\RecommendationService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Swipe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecommendationController extends Controller
{
    protected $recommendationService;
    
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }
    
    public function index()
    {
        $user = auth()->user();
        $recommendations = $this->recommendationService->getRecommendationsForUser($user);
        
        // Get the user's liked categories for the UI
        $likedCategories = $user->swipes()
            ->with('category')
            ->where('direction', 'right')
            ->whereNotNull('category_id')
            ->where('type', 'category')
            ->latest()
            ->get()
            ->pluck('category')
            ->filter() // Remove any null values
            ->unique('id');
            
        // Get product categories that might be of interest
        $likedProductCategories = $user->swipes()
            ->with('product.categories')
            ->where('direction', 'right')
            ->whereNotNull('product_id')
            ->where('type', 'product')
            ->latest()
            ->get()
            ->pluck('product.categories')
            ->flatten()
            ->filter() // Remove any null values
            ->unique('id');
        
        // Combine both sets of categories
        $allLikedCategories = $likedCategories->concat($likedProductCategories)->unique('id')->values();
        
        // For the 'because you liked' section, limit to 5 categories
        $topCategoriesForUser = $allLikedCategories->take(5);
        
        return Inertia::render('Recommendations/Index', [
            'recommendations' => $recommendations,
            'preferredCategories' => $topCategoriesForUser
        ]);
    }
}