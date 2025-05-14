<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Response;

class PDFController extends Controller
{
    /**
     * Download an order receipt as PDF.
     *
     * @param int $id
     * @return Response
     */
    public function downloadOrderReceipt($id)
    {
        $order = Order::with(['items.product', 'user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        
        // Get the HTML content of the receipt
        $html = view('pdfs.order-receipt', ['order' => $order])->render();
        
        // Generate the PDF directly using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();
        
        // Stream the PDF directly to the browser
        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="order-receipt-' . $order->order_number . '.pdf"');
    }
}