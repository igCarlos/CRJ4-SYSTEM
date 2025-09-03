<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
     protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'description',
    ];

    // Relación con Producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación con Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($movement) {
            // Actualizar stock automáticamente al crear
            if ($movement->type === 'entrada') {
                $movement->product->increment('stock', $movement->quantity);
            } elseif ($movement->type === 'salida') {
                $movement->product->decrement('stock', $movement->quantity);
            }
        });

        static::updating(function ($movement) {
            $originalType = $movement->getOriginal('type');
            $originalQuantity = $movement->getOriginal('quantity');

            $product = $movement->product;

            // Revertir el stock del estado anterior
            if ($originalType === 'entrada') {
                $product->decrement('stock', $originalQuantity);
            } elseif ($originalType === 'salida') {
                $product->increment('stock', $originalQuantity);
            }

            // Aplicar el nuevo stock según el tipo y cantidad editada
            if ($movement->type === 'entrada') {
                $product->increment('stock', $movement->quantity);
            } elseif ($movement->type === 'salida') {
                $product->decrement('stock', $movement->quantity);
            }
        });
    }

}
