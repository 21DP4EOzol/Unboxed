<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// Public routes
Route::get('/', function () {
    // Get featured products
    $featuredProducts = App\Models\Product::with('categories')
        ->where('active', true)
        ->where('featured', true)
        ->take(4)
        ->get();
    
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'featuredProducts' => $featuredProducts
    ]);
})->name('home');

// User authenticated routes
Route::middleware(['auth', 'verified', 'two-factor'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Swipe routes - require login
    Route::get('/swipe', [App\Http\Controllers\SwipeController::class, 'index'])->name('swipe.index');
    Route::post('/swipe', [App\Http\Controllers\SwipeController::class, 'store'])->name('swipe.store');
    Route::get('/swipe/history', [App\Http\Controllers\SwipeController::class, 'history'])->name('swipe.history');
    Route::delete('/swipe/remove-selected', [App\Http\Controllers\SwipeController::class, 'removeSelected'])->name('swipe.remove-selected');

    // Recommendation route - requires login
    Route::get('/recommendations', [App\Http\Controllers\RecommendationController::class, 'index'])->name('recommendations.index');

    // Cart Routes
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
    
    // Checkout Routes (New)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    
    // User Order History (New)
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
});

// Product Routes (can be viewed by guests)
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

Route::get('/categories', [App\Http\Controllers\CategoriesController::class, 'index'])->name('categories.index');

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/search/suggestions', [App\Http\Controllers\Api\SearchController::class, 'suggestions'])->name('search.suggestions');
});

Route::middleware(['auth'])->group(function () {
    Route::get('two-factor-challenge', [TwoFactorController::class, 'index'])->name('two-factor.challenge');
    Route::post('two-factor-challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('two-factor-challenge/resend', [TwoFactorController::class, 'resend'])->name('two-factor.resend');
});

// Admin routes
Route::middleware(['auth', 'verified', 'two-factor', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class);
    Route::patch('/products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
});

require __DIR__.'/auth.php';