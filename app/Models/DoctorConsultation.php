<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorConsultation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_visit_id', 'doctor_id', 'diagnosis', 'clinical_notes',
        'internal_notes', 'public_notes', 'medical_advice', 'diet_plan',
        'follow_up_date', 'show_diagnosis_to_pharmacy', 'show_prescription_notes',
        'show_reports', 'show_consultation_charges', 'show_instructions',
        'consultation_charge', 'status', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'show_diagnosis_to_pharmacy' => 'boolean',
            'show_prescription_notes' => 'boolean',
            'show_reports' => 'boolean',
            'show_consultation_charges' => 'boolean',
            'show_instructions' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function patientVisit(): BelongsTo
    {
        return $this->belongsTo(PatientVisit::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'consultation_id');
    }
}
