<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DoctorService
{
    public function listActive(?int $departmentId = null, ?int $branchId = null): Collection
    {
        return Doctor::with(['user', 'department'])
            ->where('is_available', true)
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->when($branchId, fn ($q) => $q->where(fn ($q2) => $q2->where('branch_id', $branchId)->orWhereNull('branch_id')))
            ->orderBy('id')
            ->get();
    }

    public function store(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'password'),
                'phone' => $data['phone'] ?? null,
                'branch_id' => $data['branch_id'] ?? 1,
                'department_id' => $data['department_id'] ?? null,
                'designation' => $data['designation'] ?? 'Doctor',
                'is_active' => $data['is_active'] ?? true,
            ]);

            $user->assignRole(Role::findByName('doctor', 'web'));

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'branch_id' => $data['branch_id'] ?? $user->branch_id,
                'department_id' => $data['department_id'] ?? null,
                'token_prefix' => $this->resolveTokenPrefix($data),
                'registration_number' => $data['registration_number'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'consultation_fee' => $data['consultation_fee'] ?? 0,
                'room_number' => $data['room_number'] ?? null,
                'daily_queue_limit' => $data['daily_queue_limit'] ?? null,
                'profile_photo_path' => $data['profile_photo_path'] ?? null,
                'is_available' => $data['is_available'] ?? true,
            ]);

            AuditService::log('doctor_created', $doctor, null, $doctor->toArray());

            return $doctor->load(['user', 'department']);
        });
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data) {
            $doctor->user->update(array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'is_active' => $data['is_active'] ?? null,
            ], fn ($v) => $v !== null));

            if (! empty($data['password'])) {
                $doctor->user->update(['password' => Hash::make($data['password'])]);
            }

            $doctor->update(array_filter([
                'department_id' => $data['department_id'] ?? null,
                'token_prefix' => isset($data['token_prefix']) ? strtoupper($data['token_prefix']) : null,
                'registration_number' => $data['registration_number'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'consultation_fee' => $data['consultation_fee'] ?? null,
                'room_number' => $data['room_number'] ?? null,
                'daily_queue_limit' => $data['daily_queue_limit'] ?? null,
                'profile_photo_path' => $data['profile_photo_path'] ?? null,
                'is_available' => $data['is_available'] ?? null,
            ], fn ($v) => $v !== null));

            AuditService::log('doctor_updated', $doctor);

            return $doctor->fresh(['user', 'department']);
        });
    }

    public function deactivate(Doctor $doctor): Doctor
    {
        $doctor->update(['is_available' => false]);
        $doctor->user->update(['is_active' => false]);

        AuditService::log('doctor_deactivated', $doctor);

        return $doctor;
    }

    public function activate(Doctor $doctor): Doctor
    {
        $doctor->update(['is_available' => true]);
        $doctor->user->update(['is_active' => true]);

        return $doctor;
    }

    protected function resolveTokenPrefix(array $data): ?string
    {
        if (! empty($data['token_prefix'])) {
            return strtoupper($data['token_prefix']);
        }

        if (! empty($data['department_id'])) {
            return Department::find($data['department_id'])?->code;
        }

        return null;
    }
}
