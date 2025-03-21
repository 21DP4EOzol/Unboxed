<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')
            ->latest()
            ->paginate(10);
            
        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not typically needed for orders as they're created through the checkout process
        return redirect()->route('admin.orders.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not typically needed for orders as they're created through the checkout process
        return redirect()->route('admin.orders.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['user', 'items.product'])
            ->findOrFail($id);
            
        return Inertia::render('Admin/Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::with(['user', 'items.product'])
            ->findOrFail($id);
            
        return Inertia::render('Admin/Orders/Edit', [
            'order' => $order,
            'statuses' => [
                'pending', 'processing', 'shipped', 'delivered', 'cancelled'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $order->status = $validated['status'];
        
        if (isset($validated['notes'])) {
            $order->notes = $validated['notes'];
        }
        
        $order->save();
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        
        // For safety, we'll only allow deletion of pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Only pending orders can be deleted.');
        }
        
        // Delete order items first
        $order->items()->delete();
        
        // Delete the order
        $order->delete();
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}