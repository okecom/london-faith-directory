<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'group_id',
    'title',
    'description',
    'type',
    'file_path',
    'external_url',
    'access_level',
])]
class Media extends Model
{
    use HasFactory, SoftDeletes;

    public const ACCESS_PUBLIC = 'public';
    public const ACCESS_ORGANISATION = 'organisation';
    public const ACCESS_GROUP = 'group';

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}