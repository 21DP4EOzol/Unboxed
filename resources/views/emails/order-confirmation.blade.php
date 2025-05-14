<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f3e8;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #964B00, #7D3C00);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px;
        }
        .logo {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .order-number {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f3e8;
            border-radius: 4px;
            border: 1px dashed #964B00;
            text-align: center;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 25px;
            padding-bottom: 5px;
            border-bottom: 1px solid #964B00;
            color: #964B00;
        }
        .item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .item-details {
            flex: 1;
        }
        .item-price {
            text-align: right;
            font-weight: bold;
            min-width: 100px;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .address-box {
            background-color: #f9f3e8;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #964B00, #7D3C00);
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Unboxed</div>
            <div>Order Confirmation</div>
        </div>
        
        <p>Hello {{ $user->name }},</p>
        
        <p>Thank you for your order! We're pleased to confirm that we've received your order and it's being processed.</p>
        
        <div class="order-number">
            Order #: {{ $order->order_number }}
        </div>
        
        <div class="section-title">Order Summary</div>
        
        @foreach($order->items as $item)
            <div class="item">
                <div class="item-details">
                    <strong>{{ $item->product ? $item->product->name : 'Product' }}</strong><br>
                    Quantity: {{ $item->quantity }}
                    @if($item->size || $item->color)
                        <br>
                        @if($item->size)
                            Size: {{ $item->size }}
                        @endif
                        @if($item->size && $item->color)
                            |
                        @endif
                        @if($item->color)
                            Color: {{ $item->color }}
                        @endif
                    @endif
                </div>
                <div class="item-price">${{ number_format($item->subtotal, 2) }}</div>
            </div>
        @endforeach
        
        <div class="total">
            Total: ${{ number_format($order->total_amount, 2) }}
        </div>
        
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
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('orders.show', $order->id) }}" class="button">View Order Details</a>
        </div>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact our customer support team.</p>
        
        <p>Thank you for shopping with Unboxed!</p>
        
        <div class="footer">
            <p>© {{ date('Y') }} Unboxed. All rights reserved.</p>
            <p>This email was sent to {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>