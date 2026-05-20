<?php

namespace App\Models;

use App\Enums\VisitPriority;
use App\Enums\VisitStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientVisit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'visit_number', 'patient_id', 'branch_id', 'department_id', 'doctor_id',
        'receptionist_id', 'token_number', 'queue_number', 'visit_type', 'priority',
        'status', 'weight', 'height', 'bp', 'sugar_rbs', 'temperature', 'spo2',
        'symptoms', 'chief_complaint', 'referred_by', 'notes',
        'registered_at', 'sent_to_doctor_at', 'consultation_started_at',
        'sent_to_pharmacy_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => VisitStatus::class,
            'priority' => VisitPriority::class,
            'registered_at' => 'datetime',
            'sent_to_doctor_at' => 'datetime',
            'consultation_started_at' => 'datetime',
            'sent_to_pharmacy_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function receptionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receptionist_id');
    }

    public function consultation(): HasOne
    {
        return $this->hasOne(DoctorConsultation::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function pharmacyOrders(): HasMany
    {
        return $this->hasMany(PharmacyOrder::class);
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    public function isEmergency(): bool
    {
        return $this->priority === VisitPriority::Emergency;
    }
}
