<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'denomination_id',
        'location_id',
        'description',
        'address',
        'website',
        'telephone',
        'email',
        'head',
        'photo',
    ];

    public function denomination(): BelongsTo
    {
        return $this->belongsTo(Denomination::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}