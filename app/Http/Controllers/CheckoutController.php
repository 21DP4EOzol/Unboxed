<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

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
            'payment_method' => 'required|string',
            'payment_intent_id' => 'required|string', // New field for Stripe
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
        
        // Verify payment with Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        
        try {
            $paymentIntent = PaymentIntent::retrieve($validated['payment_intent_id']);
            
            // Verify the payment is complete
            if ($paymentIntent->status !== 'succeeded') {
                return redirect()->route('checkout.index')->with('error', 'Payment was not successful. Please try again.');
            }
            
            // Create a new order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $cartTotal,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid', // Set as paid since Stripe confirmed the payment
                'notes' => $validated['notes'] ?? null,
                'payment_intent_id' => $validated['payment_intent_id'], // Store the payment intent ID
                'payment_method_details' => json_encode($paymentIntent->payment_method_details ?? null),
            ]);
            
            // Create order items (keep your existing code here)
            foreach ($cartItems as $item) {
                // Make sure we're passing the id as an integer, not a string that might contain other info
                $productId = (int)$item['id'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                ]);
                
                // Update product stock
                $product = Product::find($productId);
                if ($product) {
                    $product->stock -= $item['quantity'];
                    $product->save();
                }
            }
            
            // Clear the cart
            session()->forget('cart');
            
            // Redirect to the order confirmation page
            return redirect()->route('checkout.confirmation', $order->id);
            
        } catch (ApiErrorException $e) {
            return redirect()->route('checkout.index')->with('error', 'An error occurred while processing your payment: ' . $e->getMessage());
        }
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
            // Extract the actual product ID if it contains additional information
            $productId = is_numeric($id) ? $id : preg_replace('/[^0-9]/', '', $id);
            
            // Verify that the product still exists and has enough stock
            $product = Product::find($productId);
            
            if ($product && $product->stock >= $details['quantity']) {
                $subtotal = $details['price'] * $details['quantity'];
                
                // Make sure we capture size and color from the cart details
                $size = $details['size'] ?? null;
                $color = $details['color'] ?? null;
                
                // If size and color aren't explicitly stored, try to extract from the ID
                if (!$size || !$color) {
                    // Assuming format like "4-M-Green" where 4 is ID, M is size, Green is color
                    $parts = explode('-', $id);
                    if (count($parts) >= 3) {
                        $size = $parts[1] !== 'no-size' ? $parts[1] : null;
                        $color = $parts[2] !== 'no-color' ? $parts[2] : null;
                    }
                }
                
                $items[] = [
                    'id' => $productId,
                    'name' => $details['name'],
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                    'subtotal' => $subtotal,
                    'image' => $details['image'],
                    'size' => $size,
                    'color' => $color
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

    public function createPaymentIntent(Request $request)
    {
        // Calculate total amount from cart
        $cartItems = $this->getCartItems();
        $cartTotal = $this->calculateCartTotal($cartItems);
        
        // Amount for Stripe needs to be in cents
        $amountInCents = (int)($cartTotal * 100);
        
        Stripe::setApiKey(config('services.stripe.secret'));
        
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'metadata' => [
                    'user_id' => auth()->id()
                ],
            ]);
            
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}