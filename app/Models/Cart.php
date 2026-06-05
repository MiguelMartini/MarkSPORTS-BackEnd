<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
     protected $fillable = [
        'user_id'
     ];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'cart_product'
        )->withPivot('quantity');
    }
}
