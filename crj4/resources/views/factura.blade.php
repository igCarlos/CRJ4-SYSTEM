<div class="p-4">
    <h2 class="text-lg font-bold mb-4">Factura Venta #{{ $record->sale->id }}</h2>

    <p><strong>Cliente:</strong> {{ $record->sale->customer->name }}</p>
    <p><strong>Usuario:</strong> {{ $record->sale->user->name }}</p>
    <p><strong>Método de Pago:</strong> {{ $record->sale->payment_method }}</p>
    <p><strong>Total:</strong> {{ $record->sale->total }}</p>

    <h3 class="mt-4 font-semibold">Detalle de Productos:</h3>
    <table class="table-auto w-full border mt-2">
        <thead>
            <tr class="border-b">
                <th class="px-2 py-1">Producto</th>
                <th class="px-2 py-1">Cantidad</th>
                <th class="px-2 py-1">Precio Unitario</th>
                <th class="px-2 py-1">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->sale->sales_details as $detalle)
            <tr class="border-b">
                <td class="px-2 py-1">{{ $detalle->product->name }}</td>
                <td class="px-2 py-1">{{ $detalle->amount }}</td>
                <td class="px-2 py-1">{{ $detalle->unit_price }}</td>
                <td class="px-2 py-1">{{ $detalle->subtotal }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
