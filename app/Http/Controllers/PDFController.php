<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    public function downloadOrderReceipt($id)
    {
        // Ensure no output has been sent already
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            Log::info('Starting PDF generation for order ID: ' . $id);
            
            // Get the order with related data
            $order = Order::with(['items.product', 'user'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);
            
            Log::info('Order found: ' . $order->order_number);
            
            // Generate the filename
            $filename = 'order-receipt-' . $order->order_number . '.pdf';
            
            // Make sure PDF output is being handled correctly
            $pdf = Pdf::loadView('pdfs.order-receipt', ['order' => $order]);
            
            // Add explicit headers
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'public, must-revalidate, max-age=0',
                'Pragma' => 'public',
            ];
            
            Log::info('Returning PDF download');
            
            // Return with explicit headers
            return $pdf->download($filename, $headers);
            
        } catch (\Exception $e) {
            Log::error('PDF Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Could not generate PDF: ' . $e->getMessage());
        }
    }
}