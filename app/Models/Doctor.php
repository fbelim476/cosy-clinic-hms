<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'branch_id', 'department_id', 'token_prefix',
        'registration_number', 'specialization', 'qualification',
        'consultation_fee', 'room_number', 'daily_queue_limit',
        'profile_photo_path', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'consultation_fee' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(PatientVisit::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(DoctorConsultation::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function displayName(): string
    {
        return $this->user?->name ?? 'Doctor';
    }
}
