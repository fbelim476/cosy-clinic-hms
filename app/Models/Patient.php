<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id', 'barcode', 'name', 'mobile', 'alternate_mobile',
        'gender', 'age', 'dob', 'blood_group', 'address', 'city', 'state',
        'pincode', 'aadhaar', 'occupation', 'marital_status', 'allergies',
        'existing_diseases', 'photo_path', 'branch_id', 'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(PatientVisit::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PatientReport::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
