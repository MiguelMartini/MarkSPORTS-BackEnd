<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
   protected $table = 'orders';

    protected $fillable = [
        'discount',
        'payment_method',
        'shipping',
        'total',
        'user_id',
        'cart_id',
    ];

    protected $casts = [
        'discount' => 'double',
        'shipping' => 'decimal:2',
        'total' => 'double',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
