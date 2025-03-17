<?php

namespace App\Services\Recommendation;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

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
        // Get the categories of products the user has liked
        $likedProductIds = $user->swipes()
            ->where('direction', 'right')
            ->pluck('product_id')
            ->toArray();
            
        if (empty($likedProductIds)) {
            // If the user hasn't liked any products, return featured products
            return Product::with('categories')
                ->where('active', true)
                ->where('featured', true)
                ->inRandomOrder()
                ->take($limit)
                ->get();
        }
        
        $likedProducts = Product::with('categories')->whereIn('id', $likedProductIds)->get();
        
        // Get all category IDs from liked products
        $likedCategoryIds = $likedProducts->pluck('categories')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();
            
        // Get all products the user has already swiped on (both liked and disliked)
        $swipedProductIds = $user->swipes()
            ->pluck('product_id')
            ->toArray();
            
        // Get products that are in the same categories as the products the user liked,
        // but that the user hasn't swiped on yet
        $recommendations = Product::with('categories')
            ->whereHas('categories', function ($query) use ($likedCategoryIds) {
                $query->whereIn('categories.id', $likedCategoryIds);
            })
            ->where('active', true)
            ->whereNotIn('id', $swipedProductIds)
            ->inRandomOrder()
            ->take($limit)
            ->get();
            
        // If we don't have enough recommendations, fill with popular products
        if ($recommendations->count() < $limit) {
            $remainingCount = $limit - $recommendations->count();
            $recommendationIds = $recommendations->pluck('id')->toArray();
            
            $additionalRecommendations = Product::with('categories')
                ->where('active', true)
                ->whereNotIn('id', array_merge($swipedProductIds, $recommendationIds))
                ->inRandomOrder()
                ->take($remainingCount)
                ->get();
                
            $recommendations = $recommendations->concat($additionalRecommendations);
        }
        
        return $recommendations;
    }
}