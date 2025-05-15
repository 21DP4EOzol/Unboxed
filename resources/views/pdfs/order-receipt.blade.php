<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #964B00, #7D3C00);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin-bottom: 20px;
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
            color: #964B00;
            margin-bottom: 20px;
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
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #f9f3e8;
            color: #964B00;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #964B00;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            border-top: 2px solid #964B00;
            font-weight: bold;
            font-size: 16px;
        }
        .address-box {
            background-color: #f9f3e8;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        .info-grid {
            display: block;
            margin: 20px 0;
        }
        .info-row {
            display: block;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #964B00;
            width: 150px;
            display: inline-block;
        }
        .info-value {
            display: inline-block;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .payment-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .payment-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .payment-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .payment-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .columns {
            display: table;
            width: 100%;
        }
        .column {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Unboxed</div>
            <div>Order Receipt</div>
        </div>
        
        <div class="order-number">
            Order #: {{ $order->order_number }}
        </div>
        
        <div class="columns">
            <div class="column">
                <div class="section-title">Order Information</div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value">{{ $order->created_at->format('F j, Y') }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">{{ ucfirst($order->payment_method) }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Payment Status:</span>
                        <span class="info-value">
                            <span class="payment-badge {{ $order->payment_status == 'paid' ? 'payment-paid' : ($order->payment_status == 'pending' ? 'payment-pending' : 'payment-failed') }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Order Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="column">
                <div class="section-title">Customer Information</div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $order->user->name }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $order->user->email }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section-title">Order Items</div>
        
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product ? $item->product->name : 'Product' }}</strong>
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
                    <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-right">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="columns">
            <div class="column">
                <div class="section-title">Shipping Address</div>
                <div class="address-box">
                    {!! nl2br(e($order->shipping_address)) !!}
                </div>
            </div>
            
            <div class="column">
                <div class="section-title">Billing Address</div>
                <div class="address-box">
                    {!! nl2br(e($order->billing_address)) !!}
                </div>
            </div>
        </div>
        
        @if($order->notes)
        <div class="section-title">Order Notes</div>
        <div class="address-box">
            {{ $order->notes }}
        </div>
        @endif
        
        <div class="footer">
            <p>Thank you for shopping with Unboxed!</p>
            <p>© {{ date('Y') }} Unboxed. All rights reserved.</p>
            <p>If you have any questions, please contact us at support@unboxed.com</p>
        </div>
    </div>
</body>
</html>