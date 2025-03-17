<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => $totalUsers,
                'products' => $totalProducts,
                'orders' => $totalOrders,
            ],
            'recentOrders' => $recentOrders,
        ]);
    }
}