<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Address extends Model
{
    protected $fillable = [
        'address',
        'city',
        'state',
        'cep',
        'number'
    ];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
