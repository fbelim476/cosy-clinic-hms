<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id', 'patient_visit_id', 'consultation_id', 'uploaded_by',
        'title', 'type', 'file_path', 'is_private',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
