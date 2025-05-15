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
            
            // Use the blade template instead of inline HTML
            $pdf = PDF::loadView('pdfs.order-receipt', ['order' => $order]);
            
            Log::info('PDF loaded from template');
            
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