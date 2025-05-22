<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt</title>
    <style>
        /* Reset margins and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            font-size: 12px;
            padding: 10px;
        }
        
        .header {
            background-color: #964B00;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        
        h2 {
            font-size: 16px;
            margin: 8px 0;
            color: #964B00;
        }
        
        h3 {
            font-size: 14px;
            margin: 6px 0;
            color: #964B00;
            border-bottom: 1px solid #964B00;
            padding-bottom: 3px;
        }
        
        .info-row {
            margin-bottom: 4px;
        }
        
        .info-label {
            font-weight: bold;
            color: #964B00;
            display: inline-block;
            width: 120px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 11px;
        }
        
        th {
            background-color: #f9f3e8;
            text-align: left;
            padding: 5px;
            border-bottom: 1px solid #964B00;
        }
        
        td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .address-box {
            background-color: #f9f3e8;
            padding: 6px;
            border-radius: 4px;
            margin: 5px 0;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 10px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }
        
        .row {
            display: flex;
            margin: 0 -5px;
        }
        
        .col {
            flex: 1;
            padding: 0 5px;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Unboxed - Order Receipt</h1>
    </div>
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Order #{{ $order->order_number }}</h2>
        <div>Date: {{ $order->created_at->format('M j, Y') }}</div>
    </div>
    
    <div class="row">
        <div class="col">
            <h3>Order Information</h3>
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                {{ ucfirst($order->payment_method) }}
            </div>
            
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="badge {{ $order->payment_status == 'paid' ? 'badge-paid' : ($order->payment_status == 'pending' ? 'badge-pending' : 'badge-failed') }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Order Status:</span>
                {{ ucfirst($order->status) }}
            </div>
        </div>
        
        <div class="col">
            <h3>Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                {{ $order->user->name }}
            </div>
            
            <div class="info-row">
                <span class="info-label">Email:</span>
                {{ $order->user->email }}
            </div>
        </div>
    </div>
    
    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th width="40%">Product</th>
                <th width="20%">Price</th>
                <th width="15%">Quantity</th>
                <th width="25%">Subtotal</th>
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
                <td>€{{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>€{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="font-weight: bold;">€{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="row">
        <div class="col">
            <h3>Shipping Address</h3>
            <div class="address-box">
                {!! nl2br(e($order->shipping_address)) !!}
            </div>
        </div>
        
        <div class="col">
            <h3>Billing Address</h3>
            <div class="address-box">
                {!! nl2br(e($order->billing_address)) !!}
            </div>
        </div>
    </div>
    
    @if($order->notes)
    <h3>Order Notes</h3>
    <div class="address-box">
        {{ $order->notes }}
    </div>
    @endif
    
    <div class="footer">
        <p>Thank you for shopping with Unboxed! © {{ date('Y') }} Unboxed. All rights reserved.</p>
    </div>
</body>
</html>