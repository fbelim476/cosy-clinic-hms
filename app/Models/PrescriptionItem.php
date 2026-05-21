<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id', 'medicine_id', 'medicine_name', 'dosage',
        'frequency', 'morning', 'afternoon', 'night', 'food_timing',
        'days', 'quantity', 'notes', 'unit_price', 'gst_percent', 'sku',
    ];

    protected function casts(): array
    {
        return [
            'morning' => 'boolean',
            'afternoon' => 'boolean',
            'night' => 'boolean',
            'unit_price' => 'decimal:2',
            'gst_percent' => 'decimal:2',
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
