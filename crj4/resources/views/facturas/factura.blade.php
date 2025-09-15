<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $sale->id }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Reset / base */
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; margin: 0; padding: 0; }
        .container { width: 100%; padding: 24px; box-sizing: border-box; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .brand { display:flex; gap:12px; align-items:center; }
        .brand img { width:90px; height:auto; }
        .company { text-align: right; }
        .company h2 { margin:0; font-size:18px; }
        .details { display:flex; justify-content:space-between; margin-bottom:20px; gap:12px; }
        .box { border:1px solid #eee; padding:12px; width:48%; box-sizing:border-box; }
        table { width:100%; border-collapse: collapse; margin-top: 6px; }
        table thead th { background:#f3f3f3; padding:8px; border:1px solid #e6e6e6; text-align:left; }
        table tbody td { padding:8px; border:1px solid #e6e6e6; vertical-align: top; }
        .right { text-align:right; }
        .total { margin-top:12px; width:100%; display:flex; justify-content:flex-end; }
        .totals { width: 320px; border:1px solid #e6e6e6; padding: 8px; box-sizing:border-box; }
        .totals .row { display:flex; justify-content:space-between; padding:4px 0; }
        footer { margin-top:30px; font-size:11px; color:#666; text-align:center; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="brand">
                {{-- Si tienes logo: --}}
                {{-- <img src="{{ public_path('storage/logo.png') }}" alt="Logo"> --}}
                <div>
                    <h1>{{ config('app.name') }}</h1>
                    <div>Dirección: La misma</div>
                    <div>Tel: 1234-5678</div>
                    <div>Email: soport@crj4.com</div>
                </div>
            </div>

            <div class="company">
                <h2>FACTURA</h2>
                <div><strong>#{{ $sale->id }}</strong></div>
                <div>Fecha: {{ $sale->sale_date?->format('d/m/Y H:i') ?? $sale->created_at->format('d/m/Y H:i') }}</div>
                <div>Vendedor: {{ $sale->user?->name ?? '-' }}</div>
            </div>
        </header>

        <section class="details">
            <div class="box">
               
                <div> <strong>Cliente</strong>: {{ $sale->customer?->name ?? 'Sin cliente' }}</div>
                <div> <strong>Direccion</strong>: {{ $sale->customer?->address ?? 'Sin direccion' }}</div>
                <div> <strong>Telefono</strong>: {{ $sale->customer?->phone ?? 'Sin telefono'}}</div>
                <div> <strong>Correo</strong>: {{ $sale->customer?->email ?? 'Sin correo'}}</div>
            </div>

            <div class="box">
                <strong>Datos de venta</strong>
                <div>Método: {{ ucfirst($sale->payment_method) }}</div>
                <div>Estado: {{ ucfirst($sale->status_of_sale) }}</div>
            </div>
        </section>

        <section>
            <table>
                <thead>
                    <tr>
                        <th style="width:6%;">#</th>
                        <th>Producto</th>
                        <th style="width:10%;">Precio</th>
                        <th style="width:8%;">Cant.</th>
                        <th style="width:12%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->sales_details as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                {{ $d->product?->name ?? 'Producto eliminado' }}
                                @if($d->product?->identification ?? false)
                                    <div style="font-size:10px;color:#666;">SKU: {{ $d->product->identification }}</div>
                                @endif
                            </td>
                            <td class="right">{{ number_format($d->unit_price, 2, '.', ',') }}</td>
                            <td class="right">{{ $d->amount }}</td>
                            <td class="right">{{ number_format($d->subtotal, 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                <div class="totals">
                    <div class="row"><div>Subtotal</div><div class="right">{{ number_format($sale->sales_details->sum('subtotal'), 2, '.', ',') }}</div></div>
                    <!-- Si manejas impuestos, descuentos, añadelos -->
                    @if(isset($sale->tax) && $sale->tax)
                        <div class="row"><div>Impuesto</div><div class="right">{{ number_format($sale->tax, 2, '.', ',') }}</div></div>
                    @endif
                    <div class="row" style="font-weight:bold;"><div>Total</div><div class="right">{{ number_format($sale->total, 2, '.', ',') }}</div></div>
                </div>
            </div>
        </section>

        <footer>
            Gracias por su compra. <!-- texto legal -->
        </footer>
    </div>
</body>
</html>
