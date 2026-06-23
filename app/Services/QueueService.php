<?php

namespace App\Services;

use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\DoctorConsultation;
use App\Models\PatientVisit;
use Illuminate\Support\Collection;

class QueueService
{
    public function doctorBoard(?int $branchId = null): Collection
    {
        $doctors = Doctor::with(['user', 'department'])
            ->where('is_available', true)
            ->when($branchId, fn ($q) => $q->where(fn ($q2) => $q2->where('branch_id', $branchId)->orWhereNull('branch_id')))
            ->orderBy('department_id')
            ->get();

        return $doctors->map(function (Doctor $doctor) {
            $base = PatientVisit::where('doctor_id', $doctor->id)->whereDate('created_at', today());

            $current = (clone $base)
                ->where('status', VisitStatus::WithDoctor)
                ->latest('consultation_started_at')
                ->with('patient')
                ->first();

            return [
                'doctor' => $doctor,
                'current' => $current,
                'current_token' => $current?->displayToken(),
                'waiting' => (clone $base)->whereIn('status', [VisitStatus::Registered, VisitStatus::Waiting])->count(),
                'completed' => (clone $base)->whereIn('status', [VisitStatus::AtPharmacy, VisitStatus::Completed])->count(),
                'waiting_list' => (clone $base)
                    ->whereIn('status', [VisitStatus::Registered, VisitStatus::Waiting])
                    ->with('patient')
                    ->orderByRaw("CASE WHEN priority = 'emergency' THEN 0 ELSE 1 END")
                    ->orderBy('token_number')
                    ->get(),
            ];
        });
    }

    public function recallToken(PatientVisit $visit): PatientVisit
    {
        $visit->update([
            'status' => VisitStatus::Waiting,
            'consultation_started_at' => null,
        ]);

        $visit = $visit->fresh(['patient', 'doctor.user']);
        RealtimeService::queueUpdated('token_recalled', $visit, "Token {$visit->displayToken()} recalled");

        return $visit;
    }

    public function cancelToken(PatientVisit $visit): PatientVisit
    {
        $visit->update(['status' => VisitStatus::Cancelled]);

        $visit = $visit->fresh(['patient', 'doctor.user']);
        RealtimeService::queueUpdated('token_cancelled', $visit, "Token {$visit->displayToken()} cancelled");

        return $visit;
    }

    public function transferQueue(PatientVisit $visit, int $newDoctorId): PatientVisit
    {
        $newDoctor = Doctor::findOrFail($newDoctorId);
        $token = app(NumberGeneratorService::class)->nextDoctorToken($newDoctor);

        $visit->update([
            'doctor_id' => $newDoctor->id,
            'department_id' => $newDoctor->department_id,
            'token_number' => $token['number'],
            'token_code' => $token['code'],
            'queue_number' => $token['number'],
            'status' => VisitStatus::Waiting,
        ]);

        $visit = $visit->fresh(['patient', 'doctor.user', 'department']);
        RealtimeService::queueUpdated('queue_transferred', $visit, "Patient transferred to Dr. {$newDoctor->user->name}");

        return $visit;
    }

    public function doctorStats(Doctor $doctor): array
    {
        $base = PatientVisit::where('doctor_id', $doctor->id)->whereDate('created_at', today());

        $consultations = DoctorConsultation::where('doctor_id', $doctor->id)
            ->whereDate('created_at', today())
            ->whereNotNull('completed_at');

        return [
            'waiting' => (clone $base)->where('status', VisitStatus::Waiting)->count(),
            'in_consult' => (clone $base)->where('status', VisitStatus::WithDoctor)->count(),
            'completed' => (clone $base)->whereIn('status', [VisitStatus::AtPharmacy, VisitStatus::Completed])->count(),
            'revenue' => (float) (clone $consultations)->sum('consultation_charge'),
            'avg_minutes' => (int) round((clone $consultations)->get()->avg(function ($c) {
                if (! $c->started_at || ! $c->completed_at) {
                    return null;
                }

                return $c->started_at->diffInMinutes($c->completed_at);
            }) ?? 0),
        ];
    }
}
