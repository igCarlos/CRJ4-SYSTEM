<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class facturaController extends Controller
{
     public function show(Sale $sale)
    {
        // Cargar relaciones necesarias
        $sale->load('customer', 'user', 'sales_details.product');

        return view('facturas.factura', compact('sale'));
    }

    public function pdf(Sale $sale)
    {
        $sale->load('customer', 'user', 'sales_details.product');

        $pdf = Pdf::loadView('facturas.factura', compact('sale'))
                  ->setPaper('a4', 'portrait');

        $filename = 'factura_' . $sale->id . '.pdf';

        return $pdf->download($filename);
        // Si prefieres verlo en navegador:
        // return $pdf->stream($filename);
    }
}
