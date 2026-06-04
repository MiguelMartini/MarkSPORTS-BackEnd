<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
     protected $fillable = [
        'name',
        'description',
        'price',
        'color',
        'quantity',
        'img'
    ];

   public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(
            Cart::class,
            'cart_product'
        );
    }
}
