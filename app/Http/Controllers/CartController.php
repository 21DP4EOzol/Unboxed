<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->calculateCartTotal($cartItems);
        
        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal
        ]);
    }
    
    /**
     * Add a product to the cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $product = Product::findOrFail($validated['product_id']);
        
        // Check if we have enough stock
        if ($product->stock < $validated['quantity']) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }
        
        // Get current cart
        $cart = session()->get('cart', []);
        
        // Check if item already exists in cart
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'image' => $product->images && count($product->images) > 0 ? $product->images[0] : null
            ];
        }
        
        // Update cart
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }
    
    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $product = Product::findOrFail($validated['product_id']);
        
        // Check if we have enough stock
        if ($product->stock < $validated['quantity']) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$validated['product_id']])) {
            $cart[$validated['product_id']]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Cart updated successfully.');
        }
        
        return redirect()->back()->with('error', 'Product not found in your cart.');
    }
    
    /**
     * Remove an item from the cart
     */
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$validated['product_id']])) {
            unset($cart[$validated['product_id']]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Item removed from cart successfully.');
        }
        
        return redirect()->back()->with('error', 'Product not found in your cart.');
    }
    
    /**
     * Clear the cart
     */
    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->back()->with('success', 'Cart cleared successfully.');
    }
    
    /**
     * Get formatted cart items with product details
     */
    private function getCartItems()
    {
        $cart = session()->get('cart', []);
        $items = [];
        
        foreach ($cart as $id => $details) {
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