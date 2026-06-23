<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientVisit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function listForDate(string $date, ?int $doctorId = null): Collection
    {
        return Appointment::with(['patient', 'doctor.user', 'doctor.department'])
            ->whereDate('appointment_date', $date)
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->orderBy('appointment_time')
            ->get();
    }

    public function book(array $data): Appointment
    {
        $this->assertSlotAvailable($data['doctor_id'], $data['appointment_date'], $data['appointment_time']);

        return DB::transaction(function () use ($data) {
            $appointment = Appointment::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'department_id' => $data['department_id'] ?? Doctor::find($data['doctor_id'])?->department_id,
                'branch_id' => $data['branch_id'] ?? 1,
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'time_slot' => $data['time_slot'] ?? null,
                'status' => 'scheduled',
                'notes' => $data['notes'] ?? null,
            ]);

            AuditService::log('appointment_booked', $appointment);

            return $appointment->load(['patient', 'doctor.user']);
        });
    }

    public function checkIn(Appointment $appointment, int $receptionistId): \App\Models\PatientVisit
    {
        return DB::transaction(function () use ($appointment, $receptionistId) {
            $patient = $appointment->patient;
            $doctor = $appointment->doctor;

            $token = app(NumberGeneratorService::class)->nextDoctorToken($doctor);

            $visit = PatientVisit::create([
                'visit_number' => app(NumberGeneratorService::class)->visitNumber(),
                'patient_id' => $patient->id,
                'branch_id' => $appointment->branch_id,
                'department_id' => $appointment->department_id ?? $doctor->department_id,
                'doctor_id' => $doctor->id,
                'receptionist_id' => $receptionistId,
                'token_number' => $token['number'],
                'token_code' => $token['code'],
                'queue_number' => $token['number'],
                'visit_type' => 'opd',
                'priority' => 'normal',
                'status' => \App\Enums\VisitStatus::Waiting,
                'registered_at' => now(),
                'sent_to_doctor_at' => now(),
            ]);

            $appointment->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
                'patient_visit_id' => $visit->id,
            ]);

            RealtimeService::queueUpdated('appointment_checked_in', $visit->load('patient'), "Appointment checked in — {$visit->displayToken()}");

            return $visit;
        });
    }

    public function cancel(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'cancelled']);

        return $appointment;
    }

    protected function assertSlotAvailable(int $doctorId, string $date, string $time): void
    {
        $exists = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException('This time slot is already booked for the selected doctor.');
        }
    }
}
