<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;


class PDFController extends Controller
{
    public function downloadOrderReceipt($id)
    {
        try {
            Log::info('Starting PDF generation for order ID: ' . $id);
            
            // Get the order with related data
            $order = Order::with(['items.product', 'user'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);
            
            Log::info('Order found: ' . $order->order_number);
            
            // Generate super simple HTML for testing
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Order Receipt</title>
            </head>
            <body>
                <h1>Order Receipt</h1>
                <p>Order Number: ' . $order->order_number . '</p>
                <p>Date: ' . $order->created_at->format('Y-m-d') . '</p>
                <p>Total: $' . $order->total_amount . '</p>
            </body>
            </html>';
            
            Log::info('Generated simple HTML for testing');
            
            // Create PDF directly from the HTML string instead of using a view
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            
            Log::info('PDF loaded from HTML');
            
            // Return PDF as download with explicit headers
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="order-receipt-' . $order->order_number . '.pdf"',
            ];
            
            Log::info('Returning PDF with headers');
            
            return $pdf->download('order-receipt-' . $order->order_number . '.pdf', $headers);
            
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error('Error File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Could not generate PDF: ' . $e->getMessage());
        }
    }
}
