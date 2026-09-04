<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Denomination extends Model
{
    protected $fillable = [
        'religion_id',
        'name',
    ];

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }
}