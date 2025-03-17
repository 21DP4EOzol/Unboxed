<?php

namespace App\Http\Controllers;

use App\Services\Recommendation\RecommendationService;
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
        
        return Inertia::render('Recommendations/Index', [
            'recommendations' => $recommendations
        ]);
    }
}