<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'employee_id',
        'branch_id', 'department_id', 'designation', 'signature_path',
        'avatar', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function dashboardRoute(): string
    {
        return match (true) {
            $this->hasRole('super-admin') => route('admin.dashboard'),
            $this->hasRole('receptionist') => route('reception.dashboard'),
            $this->hasRole('doctor') => route('doctor.dashboard'),
            $this->hasRole('pharmacist') => route('pharmacy.dashboard'),
            $this->hasRole('accountant') => route('billing.dashboard'),
            $this->hasRole('lab-technician') => route('lab.dashboard'),
            $this->hasRole('nurse') => route('reception.dashboard'),
            $this->hasRole('patient') => route('patient.portal'),
            default => route('admin.dashboard'),
        };
    }
}
