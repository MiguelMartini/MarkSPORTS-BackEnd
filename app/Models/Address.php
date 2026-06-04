<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = [
        'address',
        'city',
        'state',
        'cep',
        'number'
    ];

     public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
