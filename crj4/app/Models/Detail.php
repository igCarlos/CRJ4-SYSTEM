<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Movement;

class Detail extends Model
{
    protected $table = 'sales_details';

    protected $fillable = [
        'sale_id',
        'product_id',
        'amount',
        'unit_price',
        'subtotal',
    ];

    public function sale() : BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }



protected static function booted()
{
    static::creating(function ($detail) {
        $product = $detail->product;

        if (! $product) {
            Notification::make()
                ->title("Producto no encontrado")
                ->danger()
                ->send();

            return false;
        }

        if ($detail->amount > $product->stock) {
            Notification::make()
                ->title("Stock insuficiente")
                ->body("Solo quedan {$product->stock} unidades de {$product->name}.")
                ->danger()
                ->send();

            return false; // 👉 esto evita que se guarde
        }

        // Si pasa la validación, calcula subtotal y descuenta stock
        $detail->subtotal = $detail->amount * $detail->unit_price;
        //$product->decrement('stock', $detail->amount);
        Movement::create([
            'product_id' => $detail->product_id,
            'user_id' => auth()->id(),
            'quantity' => $detail->amount, // negativo porque es salida
            'type' => 'salida',
            'description' => "Venta ID: {$detail->sale_id}",
            'created_at' => now(),
        ]);
    });

    // 🔄 En caso de eliminar el detalle, devolver stock
    static::deleted(function ($detail) {
        if ($detail->product) {
            //$detail->product->increment('stock', $detail->amount);

            Movement::create([
                'product_id' => $detail->product->id,
                'type' => 'entrada',
                'quantity' => $detail->amount,
                'description' => "Reverso de venta #{$detail->sale_id}",
                'created_at' => now(),
                'user_id' => auth()->id(),
            ]);
        }
    });
}



}
