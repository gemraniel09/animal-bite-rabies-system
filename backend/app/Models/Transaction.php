<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'place',
        'animal_id',
        'patient_id',
        'body_part',
        'category',
        'vaccination_status',
        'wash_bite',
        'rig_given',
        'booster_given',
        'animal_status',
        'brand_id',
        'age'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TransactionSchedule::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }


    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
