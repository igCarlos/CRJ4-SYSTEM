<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'image_url',
        'identification',
        'email_verified_at',
        'state',
    ];

    public function sales() : HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
