<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function sales_details() : HasMany
    {
        return $this->hasMany(Detail::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }


}
