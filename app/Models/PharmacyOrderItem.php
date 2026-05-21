<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacyOrderItem extends Model
{
    protected $fillable = [
        'pharmacy_order_id', 'medicine_id', 'prescription_item_id', 'medicine_name', 'sku',
        'quantity', 'unit_price', 'gst_percent', 'discount', 'total',
        'is_given', 'is_otc', 'notes', 'batch_number',
    ];

    public function pharmacyOrder(): BelongsTo
    {
        return $this->belongsTo(PharmacyOrder::class);
    }
}
