<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #964B00;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #964B00;
            margin-bottom: 10px;
        }
        .receipt-title {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .order-info {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #964B00;
            padding-bottom: 5px;
            color: #964B00;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f0e6d9;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            font-weight: bold;
        }
        .address-box {
            background-color: #f9f5f0;
            padding: 10px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Unboxed</div>
            <div class="receipt-title">ORDER RECEIPT</div>
            <div>Order #{{ $order->order_number }}</div>
            <div>Date: {{ $order->created_at->format('F j, Y') }}</div>
        </div>
        
        <div class="order-info">
            <div class="section-title">Customer Information</div>
            <p>
                <strong>Name:</strong> {{ $order->user->name }}<br>
                <strong>Email:</strong> {{ $order->user->email }}
            </p>
        </div>
        
        <div class="section-title">Order Items</div>
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
                                @if($item->size)
                                    Size: {{ $item->size }}
                                @endif
                                @if($item->size && $item->color)
                                    |
                                @endif
                                @if($item->color)
                                    Color: {{ $item->color }}
                                @endif
                            </small>
                        @endif
                    </td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="section-title">Shipping Information</div>
        <div class="address-box">
            {!! nl2br(e($order->shipping_address)) !!}
        </div>
        
        <div class="section-title">Billing Information</div>
        <div class="address-box">
            {!! nl2br(e($order->billing_address)) !!}
        </div>
        
        <div class="section-title">Payment Information</div>
        <p>
            <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}<br>
            <strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}
        </p>
        
        <div class="footer">
            <p>Thank you for shopping with Unboxed!</p>
            <p>© {{ date('Y') }} Unboxed. All rights reserved.</p>
        </div>
    </div>
</body>
</html>