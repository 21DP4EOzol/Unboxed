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
        
        // Get the user's liked categories from product swipes
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
        
        // For the 'because you liked' section, limit to 5 categories
        $topCategoriesForUser = $likedProductCategories->take(5);
        
        return Inertia::render('Recommendations/Index', [
            'recommendations' => $recommendations,
            'preferredCategories' => $topCategoriesForUser
        ]);
    }
}