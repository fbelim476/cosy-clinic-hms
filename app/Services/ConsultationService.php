<?php

namespace App\Services;

use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\DoctorConsultation;
use App\Models\PatientVisit;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Support\Facades\DB;

class ConsultationService
{
    public function __construct(
        protected NumberGeneratorService $numbers,
    ) {}

    public function startConsultation(PatientVisit $visit, Doctor $doctor): DoctorConsultation
    {
        $visit->update([
            'status' => VisitStatus::WithDoctor,
            'doctor_id' => $doctor->id,
            'consultation_started_at' => now(),
        ]);

        $consultation = DoctorConsultation::create([
            'patient_visit_id' => $visit->id,
            'doctor_id' => $doctor->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $visit = $visit->fresh(['patient']);
        RealtimeService::queueUpdated(
            'consultation_started',
            $visit,
            "Dr. consulting Token #{$visit->token_number}"
        );

        return $consultation;
    }

    public function saveConsultation(DoctorConsultation $consultation, array $data, array $medicines = []): DoctorConsultation
    {
        return DB::transaction(function () use ($consultation, $data, $medicines) {
            $consultation->update($data);

            if (! empty($medicines)) {
                $prescription = Prescription::create([
                    'prescription_number' => $this->numbers->prescriptionNumber(),
                    'consultation_id' => $consultation->id,
                    'patient_visit_id' => $consultation->patient_visit_id,
                    'doctor_id' => $consultation->doctor_id,
                    'visibility' => $data['prescription_visibility'] ?? 'public',
                    'instructions' => $data['instructions'] ?? null,
                    'status' => 'active',
                ]);

                foreach ($medicines as $med) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_id' => $med['medicine_id'] ?? null,
                        'medicine_name' => $med['medicine_name'],
                        'dosage' => $med['dosage'] ?? null,
                        'frequency' => $med['frequency'] ?? null,
                        'morning' => $med['morning'] ?? false,
                        'afternoon' => $med['afternoon'] ?? false,
                        'night' => $med['night'] ?? false,
                        'food_timing' => $med['food_timing'] ?? null,
                        'days' => $med['days'] ?? null,
                        'quantity' => $med['quantity'] ?? 1,
                        'notes' => $med['notes'] ?? null,
                    ]);
                }
            }

            $consultation->patientVisit->update(['status' => VisitStatus::Prescribed]);

            return $consultation->fresh(['prescriptions.items', 'patientVisit.patient']);
        });
    }

    public function sendToPharmacy(PatientVisit $visit): PatientVisit
    {
        $visit->update([
            'status' => VisitStatus::AtPharmacy,
            'sent_to_pharmacy_at' => now(),
        ]);

        AuditService::log('sent_to_pharmacy', $visit);

        $visit = $visit->fresh(['patient']);
        RealtimeService::queueUpdated(
            'sent_to_pharmacy',
            $visit,
            "Token #{$visit->token_number} ready at pharmacy"
        );

        return $visit;
    }

    public function completeConsultation(DoctorConsultation $consultation): void
    {
        $consultation->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
