<?php

use App\Models\Medicine;
use App\Models\PatientVisit;
use App\Services\ConsultationService;
use Livewire\Component;

new class extends Component
{
    public string $activeTab = 'clinical';
    public string $medicineSearch = '';
    public PatientVisit $visit;
    public string $diagnosis = '';
    public string $clinical_notes = '';
    public string $internal_notes = '';
    public string $public_notes = '';
    public string $medical_advice = '';
    public string $diet_plan = '';
    public ?string $follow_up_date = null;
    public string $instructions = '';
    public string $prescription_visibility = 'public';
    public bool $show_diagnosis_to_pharmacy = true;
    public bool $show_prescription_notes = true;
    public array $medicines = [];
    public string $med_name = '';
    public string $med_dosage = '';
    public string $med_frequency = '';
    public bool $med_morning = false;
    public bool $med_afternoon = false;
    public bool $med_night = false;
    public string $med_food = 'after';
    public int $med_days = 5;
    public int $med_qty = 1;

    public function mount(PatientVisit $visit): void
    {
        $this->visit = $visit->load(['patient', 'consultation.prescriptions.items']);
        if ($c = $this->visit->consultation) {
            $this->diagnosis = $c->diagnosis ?? '';
            $this->clinical_notes = $c->clinical_notes ?? '';
            $this->internal_notes = $c->internal_notes ?? '';
            $this->public_notes = $c->public_notes ?? '';
            $this->medical_advice = $c->medical_advice ?? '';
            $this->diet_plan = $c->diet_plan ?? '';
            $this->follow_up_date = $c->follow_up_date?->format('Y-m-d');
        }
    }

    public function addMedicine(): void
    {
        if (! $this->med_name) return;
        $this->medicines[] = [
            'medicine_name' => $this->med_name,
            'dosage' => $this->med_dosage,
            'frequency' => $this->med_frequency,
            'morning' => $this->med_morning,
            'afternoon' => $this->med_afternoon,
            'night' => $this->med_night,
            'food_timing' => $this->med_food,
            'days' => $this->med_days,
            'quantity' => $this->med_qty,
        ];
        $this->med_name = '';
    }

    public function removeMedicine(int $i): void
    {
        unset($this->medicines[$i]);
        $this->medicines = array_values($this->medicines);
    }

    public function save(): void
    {
        $consultation = $this->visit->consultation;
        if (! $consultation) {
            session()->flash('error', 'Consultation not started.');
            return;
        }

        app(ConsultationService::class)->saveConsultation($consultation, [
            'diagnosis' => $this->diagnosis,
            'clinical_notes' => $this->clinical_notes,
            'internal_notes' => $this->internal_notes,
            'public_notes' => $this->public_notes,
            'medical_advice' => $this->medical_advice,
            'diet_plan' => $this->diet_plan,
            'follow_up_date' => $this->follow_up_date,
            'show_diagnosis_to_pharmacy' => $this->show_diagnosis_to_pharmacy,
            'show_prescription_notes' => $this->show_prescription_notes,
            'instructions' => $this->instructions,
            'prescription_visibility' => $this->prescription_visibility,
        ], $this->medicines);

        app(ConsultationService::class)->completeConsultation($consultation->fresh());
        $this->dispatch('notify', title: 'Saved', message: 'Consultation saved successfully.', type: 'success');
    }

    public function sendToPharmacy(): void
    {
        $this->save();
        app(ConsultationService::class)->sendToPharmacy($this->visit->fresh());
        $this->dispatch('notify', title: 'Pharmacy', message: 'Patient sent to pharmacy queue.', type: 'success');
        $this->redirect(route('doctor.dashboard'), navigate: true);
    }

    public function with(): array
    {
        $q = $this->medicineSearch;

        return [
            'medicineList' => Medicine::where('is_active', true)
                ->when($q, fn ($b) => $b->where('name', 'like', "%{$q}%")->orWhere('generic_name', 'like', "%{$q}%"))
                ->orderBy('name')->limit(20)->get(),
            'history' => $this->visit->patient->visits()->with('consultation')->latest()->limit(8)->get(),
        ];
    }
};
?>

<div x-data @keydown.ctrl.s.prevent="$wire.save()">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 fw-bold mb-0">{{ $visit->patient->name }}</h2>
            <span class="badge bg-primary">Token #{{ $visit->token_number }}</span>
            @if($visit->isEmergency())<span class="badge bg-danger ms-1">EMERGENCY</span>@endif
        </div>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-arrow-left"></i> Queue</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="premium-card consult-sidebar p-3">
                <h5 class="fw-bold mb-3">Patient Profile</h5>
                <div class="row g-2 small">
                    <div class="col-6"><span class="text-muted">ID</span><br><strong>{{ $visit->patient->patient_id }}</strong></div>
                    <div class="col-6"><span class="text-muted">Mobile</span><br>{{ $visit->patient->mobile }}</div>
                    <div class="col-6"><span class="text-muted">Age/Sex</span><br>{{ $visit->patient->age }} / {{ ucfirst($visit->patient->gender ?? '-') }}</div>
                    <div class="col-6"><span class="text-muted">BP</span><br>{{ $visit->bp ?? '—' }}</div>
                    <div class="col-6"><span class="text-muted">SpO2</span><br>{{ $visit->spo2 ?? '—' }}%</div>
                    <div class="col-6"><span class="text-muted">Sugar</span><br>{{ $visit->sugar_rbs ?? '—' }}</div>
                </div>
                <hr>
                <p class="small mb-1"><strong>Complaint</strong></p>
                <p class="small text-muted">{{ $visit->chief_complaint }}</p>
                <h6 class="fw-bold mt-3 mb-2">Timeline</h6>
                @foreach($history as $h)
                    <div class="timeline-item small">
                        <strong>{{ $h->created_at->format('d M Y') }}</strong><br>
                        {{ Str::limit($h->chief_complaint, 40) }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-8">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><button type="button" class="nav-link {{ $activeTab==='clinical'?'active':'' }}" wire:click="$set('activeTab','clinical')">Clinical</button></li>
                <li class="nav-item"><button type="button" class="nav-link {{ $activeTab==='rx'?'active':'' }}" wire:click="$set('activeTab','rx')">Prescription</button></li>
            </ul>
            <form wire:submit.prevent="save">
                @if($activeTab === 'clinical')
                <div class="premium-card p-4">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">Diagnosis</label><textarea wire:model="diagnosis" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Clinical Notes (Private)</label><textarea wire:model="clinical_notes" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Public Notes</label><textarea wire:model="public_notes" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Medical Advice</label><textarea wire:model="medical_advice" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Diet Plan</label><textarea wire:model="diet_plan" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-4"><label class="form-label">Follow-up Date</label><input type="date" wire:model="follow_up_date" class="form-control"></div>
                        <div class="col-md-4">
                            <label class="form-label">Prescription Visibility</label>
                            <select wire:model="prescription_visibility" class="form-select">
                                <option value="public">Public (Pharmacy Visible)</option>
                                <option value="private">Private (Doctor Only)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check mt-4"><input type="checkbox" wire:model="show_diagnosis_to_pharmacy" class="form-check-input"> Show diagnosis to pharmacy</label>
                        </div>
                    </div>
                </div>
                @else
                <div class="premium-card p-4">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input type="text" wire:model.live.debounce.300ms="medicineSearch" class="form-control" placeholder="Search medicines...">
                                <input wire:model="med_name" class="form-control mt-1" placeholder="Medicine name">
                                @if($medicineSearch && $medicineList->count())
                                    <div class="list-group mt-1 shadow-sm" style="max-height:120px;overflow:auto">
                                        @foreach($medicineList as $m)
                                            <button type="button" class="list-group-item list-group-item-action py-1 small" wire:click="$set('med_name','{{ $m->name }}')">{{ $m->name }}</button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-2"><input wire:model="med_dosage" class="form-control" placeholder="Dosage"></div>
                            <div class="col-md-2"><input wire:model="med_frequency" class="form-control" placeholder="1-0-1"></div>
                            <div class="col-md-1"><label class="form-check"><input type="checkbox" wire:model="med_morning" class="form-check-input">M</label></div>
                            <div class="col-md-1"><label class="form-check"><input type="checkbox" wire:model="med_afternoon" class="form-check-input">A</label></div>
                            <div class="col-md-1"><label class="form-check"><input type="checkbox" wire:model="med_night" class="form-check-input">N</label></div>
                            <div class="col-md-1"><button type="button" wire:click="addMedicine" class="btn btn-outline-primary w-100">+</button></div>
                        </div>
                        <table class="table table-sm">
                            <thead><tr><th>Medicine</th><th>Dosage</th><th>Schedule</th><th>Days</th><th></th></tr></thead>
                            <tbody>
                                @foreach($medicines as $i => $med)
                                    <tr>
                                        <td>{{ $med['medicine_name'] }}</td>
                                        <td>{{ $med['dosage'] }}</td>
                                        <td>{{ ($med['morning']?'M':'') . ($med['afternoon']?'A':'') . ($med['night']?'N':'') }}</td>
                                        <td>{{ $med['days'] }}</td>
                                        <td><button type="button" wire:click="removeMedicine({{ $i }})" class="btn btn-sm btn-ghost-danger">×</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <textarea wire:model="instructions" class="form-control mt-2" placeholder="Prescription instructions..." rows="2"></textarea>
                </div>
                @endif

                <button type="submit" class="fab-save d-none d-md-inline-flex" wire:loading.attr="disabled">
                    <i class="ti ti-device-floppy me-1"></i> Save
                </button>
                <div class="premium-card mt-3 p-3 d-flex flex-wrap gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" wire:click="sendToPharmacy" class="btn btn-success"><i class="ti ti-send"></i> Send to Pharmacy</button>
                    <a href="{{ route('print.prescription', $visit) }}" target="_blank" class="btn btn-outline-secondary"><i class="ti ti-printer"></i></a>
                </div>
            </form>
        </div>
    </div>
</div>
@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
