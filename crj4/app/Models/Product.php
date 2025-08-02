<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use Notifiable;
    
    protected $fillable = [
        'name',
        'code',
        'description',
        'category_id',
        'supplier_id',
        'sales_price',
        'stock',
        'state',
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    protected static function booted()
    {
        static::creating(function ($producto) {
            $producto->code = 'PROD-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        });
    }

}
