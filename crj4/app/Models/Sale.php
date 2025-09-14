<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'sale_date',
        'total',
        'payment_method',
        'status_of_sale',
        'state',
    ];

    public function sales_details()
    {
        return $this->hasMany(Detail::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
