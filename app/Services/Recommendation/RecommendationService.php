<?php

namespace App\Services\Recommendation;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Generate product recommendations for a user based on their swipe history
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getRecommendationsForUser(User $user, int $limit = 10): Collection
    {
        // Get products the user has already swiped on
        $swipedProductIds = $user->swipes()
            ->whereNotNull('product_id')
            ->pluck('product_id')
            ->toArray();
            
        // Get the categories of products the user has liked
        $likedProductIds = $user->swipes()
            ->where('direction', 'right')
            ->whereNotNull('product_id')
            ->pluck('product_id')
            ->toArray();
            
        // Get the categories that the user has explicitly liked
        $likedCategoryIds = $user->swipes()
            ->where('direction', 'right')
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->toArray();
            
        // If user hasn't liked any products or categories, return featured products
        if (empty($likedProductIds) && empty($likedCategoryIds)) {
            return Product::with('categories')
                ->where('active', true)
                ->where('featured', true)
                ->inRandomOrder()
                ->take($limit)
                ->get();
        }
        
        // Get category IDs from liked products to combine with explicitly liked categories
        $likedProductCategories = [];
        if (!empty($likedProductIds)) {
            $likedProducts = Product::with('categories')->whereIn('id', $likedProductIds)->get();
            $likedProductCategories = $likedProducts->pluck('categories')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->toArray();
        }
        
        // Combine both sources of liked categories
        $allLikedCategoryIds = array_unique(array_merge($likedProductCategories, $likedCategoryIds));
        
        // Start with a base query for all active products
        $productsQuery = Product::with('categories')
            ->where('active', true)
            ->whereNotIn('id', $swipedProductIds);
            
        // Calculate a score for each product based on category matches
        if (!empty($allLikedCategoryIds)) {
            // First get products in exact categories the user likes
            // We'll use this as the primary recommendation source
            $recommendations = $productsQuery->clone()
                ->whereHas('categories', function ($query) use ($allLikedCategoryIds) {
                    $query->whereIn('categories.id', $allLikedCategoryIds);
                })
                ->inRandomOrder()
                ->take($limit)
                ->get();
                
            // If we don't have enough recommendations, add products from categories similar to what they like
            if ($recommendations->count() < $limit) {
                $remainingCount = $limit - $recommendations->count();
                $recommendationIds = $recommendations->pluck('id')->toArray();
                
                // Get categories similar to what the user likes
                // This could be based on a category hierarchy or other similarity metric
                // For simplicity, we'll just get other categories for now
                $similarCategoryIds = Category::whereNotIn('id', $allLikedCategoryIds)
                    ->where('active', true)
                    ->inRandomOrder()
                    ->take(5)
                    ->pluck('id')
                    ->toArray();
                
                // Get products from similar categories
                $additionalRecommendations = $productsQuery->clone()
                    ->whereNotIn('id', array_merge($swipedProductIds, $recommendationIds))
                    ->whereHas('categories', function ($query) use ($similarCategoryIds) {
                        $query->whereIn('categories.id', $similarCategoryIds);
                    })
                    ->inRandomOrder()
                    ->take($remainingCount)
                    ->get();
                    
                $recommendations = $recommendations->concat($additionalRecommendations);
            }
            
            // If we still don't have enough, add random popular products
            if ($recommendations->count() < $limit) {
                $remainingCount = $limit - $recommendations->count();
                $recommendationIds = $recommendations->pluck('id')->toArray();
                
                // Get random popular products
                $randomRecommendations = $productsQuery->clone()
                    ->whereNotIn('id', array_merge($swipedProductIds, $recommendationIds))
                    ->inRandomOrder()
                    ->take($remainingCount)
                    ->get();
                    
                $recommendations = $recommendations->concat($randomRecommendations);
            }
            
            return $recommendations;
        } else {
            // Fallback to featured products if we had issues
            return Product::with('categories')
                ->where('active', true)
                ->where('featured', true)
                ->whereNotIn('id', $swipedProductIds)
                ->inRandomOrder()
                ->take($limit)
                ->get();
        }
    }
}