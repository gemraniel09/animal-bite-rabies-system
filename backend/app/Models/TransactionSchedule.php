<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionSchedule extends Model
{
    protected $fillable = [
        'transaction_id',
        'name',
        'schedule',
        'visited_date',
        'remarks',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
