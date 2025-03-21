<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page
     */
    public function index()
    {
        // Get cart items
        $cartItems = $this->getCartItems();
        $cartTotal = $this->calculateCartTotal($cartItems);
        
        // If cart is empty, redirect to cart page
        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        return Inertia::render('Checkout/Index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal
        ]);
    }
    
    /**
     * Process the checkout
     */
    public function process(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'payment_method' => 'required|in:credit_card,paypal',
            'notes' => 'nullable|string',
        ]);
        
        // Get cart items
        $cartItems = $this->getCartItems();
        
        // If cart is empty, redirect to cart page
        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        // Calculate cart total
        $cartTotal = $this->calculateCartTotal($cartItems);
        
        // Create a new order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'total_amount' => $cartTotal,
            'status' => 'pending',
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending', // Initially set to pending
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Create order items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
            
            // Update product stock
            $product = Product::find($item['id']);
            if ($product) {
                $product->stock -= $item['quantity'];
                $product->save();
            }
        }
        
        // In a real application, you would process the payment here
        // and update the payment_status accordingly
        
        // For simplicity, we'll just simulate a successful payment
        $order->payment_status = 'paid';
        $order->save();
        
        // Clear the cart
        session()->forget('cart');
        
        // Redirect to the order confirmation page
        return redirect()->route('checkout.confirmation', $order->id);
    }
    
    /**
     * Display the order confirmation page
     */
    public function confirmation($orderId)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($orderId);
        
        // Check if the order belongs to the logged-in user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return Inertia::render('Checkout/Confirmation', [
            'order' => $order
        ]);
    }
    
    /**
     * Get formatted cart items with product details
     */
    private function getCartItems()
    {
        $cart = session()->get('cart', []);
        $items = [];
        
        foreach ($cart as $id => $details) {
            // Verify that the product still exists and has enough stock
            $product = Product::find($id);
            
            if ($product && $product->stock >= $details['quantity']) {
                $subtotal = $details['price'] * $details['quantity'];
                
                $items[] = [
                    'id' => $id,
                    'name' => $details['name'],
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                    'subtotal' => $subtotal,
                    'image' => $details['image']
                ];
            }
        }
        
        return $items;
    }
    
    /**
     * Calculate the total cart value
     */
    private function calculateCartTotal($cartItems)
    {
        $total = 0;
        
        foreach ($cartItems as $item) {
            $total += $item['subtotal'];
        }
        
        return $total;
    }
}