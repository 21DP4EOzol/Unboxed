<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        $categories = Category::where('active', true)->get();
        
        return Inertia::render('Categories/Index', [
            'categories' => $categories
        ]);
    }
}