<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientVisit;
use Illuminate\Support\Facades\DB;

class NumberGeneratorService
{
    public function patientId(): string
    {
        $count = Patient::withTrashed()->count() + 1;

        return 'PAT' . date('y') . str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }

    public function visitNumber(): string
    {
        $count = PatientVisit::withTrashed()->whereDate('created_at', today())->count() + 1;

        return 'VIS' . date('ymd') . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function prescriptionNumber(): string
    {
        return 'RX' . date('ymdHis') . rand(10, 99);
    }

    public function invoiceNumber(): string
    {
        return 'INV' . date('ymd') . str_pad((string) (DB::table('invoices')->count() + 1), 5, '0', STR_PAD_LEFT);
    }

    public function nextToken(?int $branchId = null, ?int $departmentId = null): int
    {
        $query = PatientVisit::whereDate('created_at', today());

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return (int) $query->max('token_number') + 1;
    }

    public function nextDoctorToken(Doctor $doctor): array
    {
        $next = (int) PatientVisit::where('doctor_id', $doctor->id)
            ->whereDate('created_at', today())
            ->max('token_number') + 1;

        $prefix = strtoupper($doctor->token_prefix ?: $doctor->department?->code ?: 'TKN');

        return [
            'number' => $next,
            'code' => $prefix . '-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT),
        ];
    }
}
