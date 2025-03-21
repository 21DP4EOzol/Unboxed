<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
            
        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }
    
    /**
     * Display the specified order details
     */
    public function show($id)
    {
        $order = Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
            
        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }
}