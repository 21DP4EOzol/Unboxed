<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SwipeController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    
    Route::get('/orders/{order}/receipt', [App\Http\Controllers\PDFController::class, 'downloadOrderReceipt'])
        ->name('orders.receipt');

    Route::get('/test-pdf', function () {
    $html = '<h1>Test PDF</h1><p>This is a test PDF document.</p>';
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
    
    $content = $pdf->output();
    
    $response = new \Illuminate\Http\Response($content);
    $response->header('Content-Type', 'application/pdf');
    $response->header('Content-Disposition', 'attachment; filename="test.pdf"');
    $response->header('Content-Length', strlen($content));
    
    return $response;
    });

    

    Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])
    ->name('checkout.payment-intent');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Swipe routes - require login
    Route::get('/swipe', [SwipeController::class, 'index'])->name('swipe.index');
    Route::post('/swipe', [SwipeController::class, 'store'])->name('swipe.store');
    Route::get('/swipe/history', [SwipeController::class, 'history'])->name('swipe.history');
    Route::delete('/swipe/remove-selected', [SwipeController::class, 'removeSelected'])->name('swipe.remove-selected');

    // Recommendation route - requires login
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');

    // Cart Routes
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
    
    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    
    // User Order History
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    
    // Two Factor Authentication Routes
    Route::prefix('two-factor')->name('two-factor.')->group(function() {
        Route::get('challenge', [TwoFactorController::class, 'index'])->name('challenge');
        Route::post('verify', [TwoFactorController::class, 'verify'])->name('verify');
        Route::post('resend', [TwoFactorController::class, 'resend'])->name('resend');
        
        
        // New routes for enabling/disabling
        Route::post('enable', function () {
            $user = Auth::user();
            $user->generateTwoFactorCode();
            $user->notify(new App\Notifications\TwoFactorCode());
            return response()->json(['message' => 'Verification code sent to your email']);
        })->name('enable');
        
        Route::post('confirm', function (Illuminate\Http\Request $request) {
            $user = Auth::user();
            
            if (!$user->two_factor_code || !$user->two_factor_expires_at) {
                return back()->withErrors(['code' => 'No verification code found. Please try again.']);
            }
            
            if ($request->code == $user->two_factor_code && 
                \Carbon\Carbon::parse($user->two_factor_expires_at)->gt(now())) {
                
                $user->forceFill([
                    'two_factor_confirmed_at' => now(),
                ])->save();
                
                $user->resetTwoFactorCode();
                
                return back();
            }
            
            return back()->withErrors(['code' => 'The verification code is invalid or has expired.']);
        })->name('confirm');
        
        Route::delete('disable', function () {
            $user = Auth::user();
            $user->forceFill([
                'two_factor_confirmed_at' => null,
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
            ])->save();
            
            return back();
        })->name('disable');
    });
});

// Product Routes (can be viewed by guests)
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

Route::get('/categories', [App\Http\Controllers\CategoriesController::class, 'index'])->name('categories.index');

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/search/suggestions', [App\Http\Controllers\Api\SearchController::class, 'suggestions'])->name('search.suggestions');
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class);
    Route::patch('/products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
});

require __DIR__.'/auth.php';