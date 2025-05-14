<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];
    function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
