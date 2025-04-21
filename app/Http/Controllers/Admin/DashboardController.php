<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic stats
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        
        //based on last time logged in(30mins)
        $activeThreshold = now()->subMinutes(30);
        $activeUsers = User::where('last_login_at', '>=', $activeThreshold)->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        
        // Get top selling products (based on order items)
        $topSellingProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();
            
        // Get least selling products
        $leastSellingProducts = Product::select('products.*', 
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->where('products.active', true) // Only include active products
            ->groupBy('products.id')
            ->orderBy('total_sold', 'asc')
            ->take(5)
            ->get();
        
        // Product availability stats
        $inStockProducts = Product::where('stock', '>', 0)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();
        
        // Order status breakdown
        $orderStatusBreakdown = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        // Calculate revenue
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        
        // Recent orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => [
                    'total' => $totalUsers,
                    'active' => $activeUsers,
                    'inactive' => $inactiveUsers,
                ],
                'products' => [
                    'total' => $totalProducts,
                    'inStock' => $inStockProducts,
                    'outOfStock' => $outOfStockProducts,
                ],
                'orders' => [
                    'total' => $totalOrders,
                    'statusBreakdown' => $orderStatusBreakdown,
                ],
                'revenue' => $totalRevenue,
            ],
            'topSellingProducts' => $topSellingProducts,
            'leastSellingProducts' => $leastSellingProducts,
            'recentOrders' => $recentOrders,
        ]);
    }
}