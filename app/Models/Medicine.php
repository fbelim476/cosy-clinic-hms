<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'generic_name', 'sku', 'barcode', 'category', 'medicine_type',
        'manufacturer', 'unit', 'strength', 'mrp', 'selling_price', 'purchase_price',
        'gst_percent', 'reorder_level', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'mrp' => 'decimal:2',
            'gst_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(MedicineBatch::class);
    }

    public function stockQuantity(): int
    {
        return (int) $this->batches()->sum('quantity');
    }
}
