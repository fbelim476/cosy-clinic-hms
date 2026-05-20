<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'generic_name', 'sku', 'barcode', 'category',
        'manufacturer', 'unit', 'mrp', 'selling_price', 'gst_percent',
        'reorder_level', 'is_active',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(MedicineBatch::class);
    }

    public function stockQuantity(): int
    {
        return (int) $this->batches()->sum('quantity');
    }
}
