<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Religion extends Model
{
    protected $fillable = [
        'name',
    ];

    public function denominations(): HasMany
    {
        return $this->hasMany(Denomination::class);
    }
}