<?php

namespace App\Services;

use App\Enums\VisitPriority;
use App\Enums\VisitStatus;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Repositories\PatientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PatientService
{
    public function __construct(
        protected PatientRepository $patients,
        protected NumberGeneratorService $numbers,
    ) {}

    public function search(string $query, int $limit = 10)
    {
        return $this->patients->search($query, $limit);
    }

    public function findByMobile(string $mobile): ?Patient
    {
        return $this->patients->findByMobile($mobile);
    }

    public function register(array $patientData, array $visitData, int $receptionistId): PatientVisit
    {
        return DB::transaction(function () use ($patientData, $visitData, $receptionistId) {
            $patient = isset($patientData['id'])
                ? $this->patients->findOrFail($patientData['id'])
                : $this->createPatient($patientData);

            $tokenNumber = $this->numbers->nextToken(
                $visitData['branch_id'] ?? null,
                $visitData['department_id'] ?? null
            );

            $visit = PatientVisit::create([
                'visit_number' => $this->numbers->visitNumber(),
                'patient_id' => $patient->id,
                'branch_id' => $visitData['branch_id'] ?? $patient->branch_id,
                'department_id' => $visitData['department_id'] ?? null,
                'doctor_id' => $visitData['doctor_id'] ?? null,
                'receptionist_id' => $receptionistId,
                'token_number' => $tokenNumber,
                'queue_number' => $tokenNumber,
                'visit_type' => $visitData['visit_type'] ?? 'opd',
                'priority' => $visitData['priority'] ?? VisitPriority::Normal,
                'status' => VisitStatus::Registered,
                'weight' => $visitData['weight'] ?? null,
                'height' => $visitData['height'] ?? null,
                'bp' => $visitData['bp'] ?? null,
                'sugar_rbs' => $visitData['sugar_rbs'] ?? null,
                'temperature' => $visitData['temperature'] ?? null,
                'spo2' => $visitData['spo2'] ?? null,
                'symptoms' => $visitData['symptoms'] ?? null,
                'chief_complaint' => $visitData['chief_complaint'] ?? null,
                'referred_by' => $visitData['referred_by'] ?? null,
                'notes' => $visitData['notes'] ?? null,
                'registered_at' => now(),
            ]);

            AuditService::log('patient_registered', $visit, null, $visit->toArray());

            $visit = $visit->load(['patient', 'doctor.user', 'department']);
            RealtimeService::queueUpdated(
                'patient_registered',
                $visit,
                "New patient {$visit->patient->name} — Token #{$visit->token_number}"
            );

            return $visit;
        });
    }

    public function sendToDoctor(PatientVisit $visit): PatientVisit
    {
        $visit->update([
            'status' => VisitStatus::Waiting,
            'sent_to_doctor_at' => now(),
        ]);

        AuditService::log('sent_to_doctor', $visit);

        $visit = $visit->fresh(['patient']);
        RealtimeService::queueUpdated(
            'sent_to_doctor',
            $visit,
            "Token #{$visit->token_number} — {$visit->patient->name} sent to doctor"
        );

        return $visit;
    }

    protected function createPatient(array $data): Patient
    {
        $patientId = $this->numbers->patientId();
        $barcode = 'CC' . str_pad((string) (Patient::max('id') + 1), 8, '0', STR_PAD_LEFT);

        return Patient::create([
            'patient_id' => $patientId,
            'barcode' => $barcode,
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'alternate_mobile' => $data['alternate_mobile'] ?? null,
            'gender' => $data['gender'] ?? null,
            'age' => $data['age'] ?? null,
            'dob' => $data['dob'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'pincode' => $data['pincode'] ?? null,
            'aadhaar' => $data['aadhaar'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'allergies' => $data['allergies'] ?? null,
            'existing_diseases' => $data['existing_diseases'] ?? null,
            'photo_path' => $data['photo_path'] ?? null,
            'branch_id' => $data['branch_id'] ?? 1,
            'registered_by' => $data['registered_by'] ?? auth()->id(),
        ]);
    }

    public function generateOpdQr(PatientVisit $visit): string
    {
        try {
            return QrCode::size(120)->generate($visit->visit_number);
        } catch (\Throwable) {
            return '<div style="font-size:10px;border:1px solid #ccc;padding:8px">'.$visit->visit_number.'</div>';
        }
    }
}
