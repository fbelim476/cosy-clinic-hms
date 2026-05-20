<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PharmacyOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'patient_visit_id', 'prescription_id',
        'pharmacist_id', 'status', 'subtotal', 'discount', 'tax',
        'total', 'notes', 'completed_at',
    ];

    public function patientVisit(): BelongsTo
    {
        return $this->belongsTo(PatientVisit::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PharmacyOrderItem::class);
    }
}
