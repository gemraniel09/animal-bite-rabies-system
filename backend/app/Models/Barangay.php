<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    protected $fillable = [
        'id',
        'code',
        'name'
    ];

    protected $appends = ['transaction_count'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTransactionCountAttribute(): int
    {
        return $this->transactions()->count();
    }
}
