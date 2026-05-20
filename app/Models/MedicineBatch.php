<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineBatch extends Model
{
    protected $fillable = [
        'medicine_id', 'batch_number', 'expiry_date', 'quantity',
        'purchase_price', 'selling_price', 'branch_id',
    ];

    protected function casts(): array
    {
        return ['expiry_date' => 'date'];
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
