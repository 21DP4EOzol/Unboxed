<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12pt; 
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 2px solid #964B00;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #964B00;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left;
        }
        th { 
            background-color: #f2f2f2; 
        }
        .total { 
            font-weight: bold; 
        }
        .section {
            margin-top: 25px;
            margin-bottom: 10px;
        }
        h2 {
            color: #964B00;
            border-bottom: 1px solid #964B00;
            padding-bottom: 5px;
        }
        .address-box {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10pt;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Unboxed</div>
        <h1>Order Receipt</h1>
        <p><strong>Order #{{ $order->order_number }}</strong></p>
        <p>Date: {{ $order->created_at->format('F j, Y') }}</p>
    </div>
    
    <div class="section">
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
                    <td>
                        {{ $item->product ? $item->product->name : 'Product' }}
                        @if($item->size || $item->color)
                            <br>
                            <small>
                                @if($item->size) Size: {{ $item->size }} @endif
                                @if($item->size && $item->color) | @endif
                                @if($item->color) Color: {{ $item->color }} @endif
                            </small>
                        @endif
                    </td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total">
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>Customer Information</h2>
        <p><strong>Name:</strong> {{ $order->user->name }}</p>
        <p><strong>Email:</strong> {{ $order->user->email }}</p>
    </div>
    
    <div class="section">
        <h2>Shipping Address</h2>
        <div class="address-box">
            {!! nl2br(e($order->shipping_address)) !!}
        </div>
    </div>
    
    <div class="section">
        <h2>Payment Information</h2>
        <p><strong>Method:</strong> {{ ucfirst($order->payment_method) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->payment_status) }}</p>
    </div>
    
    <div class="footer">
        <p>Thank you for shopping with Unboxed!</p>
        <p>© {{ date('Y') }} Unboxed. All rights reserved.</p>
    </div>
</body>
</html>