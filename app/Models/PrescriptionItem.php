<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id', 'medicine_id', 'medicine_name', 'dosage',
        'frequency', 'morning', 'afternoon', 'night', 'food_timing',
        'days', 'quantity', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'morning' => 'boolean',
            'afternoon' => 'boolean',
            'night' => 'boolean',
        ];
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
