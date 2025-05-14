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
            // Get the order with related data
            $order = Order::with(['items.product', 'user'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);

            // Generate the filename
            $filename = 'order-receipt-' . $order->order_number . '.pdf';

            // Load the view and set PDF options
            $pdf = Pdf::loadView('pdfs.order-receipt', [
                'order' => $order
            ])->setPaper('a4', 'portrait')
              ->setOptions([
                  'isHtml5ParserEnabled' => true,
                  'isRemoteEnabled' => true,
                  'defaultFont' => 'sans-serif'
              ]);

            // Force download of the PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Could not generate PDF: ' . $e->getMessage());
        }
    }
}
