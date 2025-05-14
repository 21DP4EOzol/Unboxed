<!-- resources/views/pdfs/order-receipt-simple.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Receipt</h1>
        <p>Order #{{ $order->order_number }}</p>
        <p>Date: {{ $order->created_at->format('F j, Y') }}</p>
    </div>
    
    <h2>Items</h2>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product ? $item->product->name : 'Product' }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="3" style="text-align: right;">Total:</td>
                <td>${{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <h2>Customer Information</h2>
    <p>Name: {{ $order->user->name }}</p>
    <p>Email: {{ $order->user->email }}</p>
    
    <h2>Shipping Address</h2>
    <p>{!! nl2br(e($order->shipping_address)) !!}</p>
    
    <h2>Payment Information</h2>
    <p>Method: {{ ucfirst($order->payment_method) }}</p>
    <p>Status: {{ ucfirst($order->payment_status) }}</p>
</body>
</html>