<?php

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\PatientService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public string $mode = 'opd';
    public bool $isExisting = false;
    public ?int $patientId = null;
    public string $search = '';
    public array $searchResults = [];
    public bool $duplicateWarning = false;

    public string $name = '';
    public string $mobile = '';
    public string $alternate_mobile = '';
    public string $gender = 'male';
    public ?int $age = null;
    public ?string $dob = null;
    public string $blood_group = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $pincode = '';
    public string $chief_complaint = '';
    public string $symptoms = '';
    public string $bp = '';
    public string $sugar_rbs = '';
    public ?float $temperature = null;
    public ?int $spo2 = null;
    public ?float $weight = null;
    public ?float $height = null;
    public string $visit_type = 'opd';
    public string $priority = 'normal';
    public ?int $department_id = null;
    public ?int $doctor_id = null;
    public $photo;
    public ?int $printVisitId = null;
    public bool $showPrintModal = false;

    public function updatedSearch(): void
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }
        $this->searchResults = app(PatientService::class)->search($this->search)
            ->map(fn ($p) => ['id' => $p->id, 'patient_id' => $p->patient_id, 'name' => $p->name, 'mobile' => $p->mobile, 'age' => $p->age])
            ->toArray();
    }

    public function updatedMobile(): void
    {
        if (strlen($this->mobile) >= 10 && ! $this->isExisting) {
            $this->duplicateWarning = app(PatientService::class)->findByMobile($this->mobile) !== null;
        } else {
            $this->duplicateWarning = false;
        }
    }

    public function selectPatient(int $id): void
    {
        $patient = Patient::findOrFail($id);
        $this->isExisting = true;
        $this->patientId = $patient->id;
        $this->name = $patient->name;
        $this->mobile = $patient->mobile;
        $this->gender = $patient->gender ?? 'male';
        $this->age = $patient->age;
        $this->search = '';
        $this->searchResults = [];
        $this->duplicateWarning = false;
        $this->step = 2;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        if ($mode === 'emergency') {
            $this->priority = 'emergency';
            $this->visit_type = 'emergency';
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate(['name' => 'required|min:2', 'mobile' => 'required|digits_between:10,15']);
            if ($this->duplicateWarning) return;
        }
        $this->step = min(3, $this->step + 1);
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function register(): void
    {
        $this->validate(['name' => 'required', 'mobile' => 'required', 'gender' => 'required']);

        $photoPath = $this->photo ? $this->photo->store('patients', 'public') : null;

        $visit = app(PatientService::class)->register(
            $this->isExisting ? ['id' => $this->patientId] : array_filter([
                'name' => $this->name, 'mobile' => $this->mobile, 'gender' => $this->gender,
                'age' => $this->age, 'dob' => $this->dob, 'address' => $this->address,
                'city' => $this->city, 'branch_id' => auth()->user()->branch_id ?? 1,
                'registered_by' => auth()->id(), 'photo_path' => $photoPath,
            ]),
            [
                'visit_type' => $this->visit_type, 'priority' => $this->priority,
                'department_id' => $this->department_id, 'doctor_id' => $this->doctor_id,
                'chief_complaint' => $this->chief_complaint, 'symptoms' => $this->symptoms,
                'bp' => $this->bp, 'sugar_rbs' => $this->sugar_rbs,
                'temperature' => $this->temperature, 'spo2' => $this->spo2,
                'weight' => $this->weight, 'height' => $this->height,
                'branch_id' => auth()->user()->branch_id ?? 1,
            ],
            auth()->id()
        );

        $this->printVisitId = $visit->id;
        $this->showPrintModal = true;
        $this->dispatch('notify', title: 'Patient Registered Successfully', message: "Token #{$visit->token_number} generated", type: 'success');
        $this->dispatch('open-opd-print', url: route('print.opd-slip', ['visit' => $visit, 'embed' => 1]));
    }

    public function closePrintAndReset(): void
    {
        $this->showPrintModal = false;
        $this->printVisitId = null;
        $this->reset([
            'step', 'mode', 'isExisting', 'patientId', 'search', 'searchResults', 'duplicateWarning',
            'name', 'mobile', 'alternate_mobile', 'gender', 'age', 'dob', 'blood_group',
            'address', 'city', 'state', 'pincode', 'chief_complaint', 'symptoms',
            'bp', 'sugar_rbs', 'temperature', 'spo2', 'weight', 'height',
            'visit_type', 'priority', 'department_id', 'doctor_id', 'photo',
        ]);
        $this->step = 1;
        $this->mode = 'opd';
        $this->gender = 'male';
        $this->visit_type = 'opd';
        $this->priority = 'normal';
    }

    public function with(): array
    {
        return [
            'departments' => Department::where('is_active', true)->get(),
            'doctors' => Doctor::with('user')->where('is_available', true)->get(),
        ];
    }
};
?>

<div class="cc-patient-register">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h2 class="h3 fw-bold mb-0">Patient Registration</h2>
        <div class="btn-group">
            <button type="button" wire:click="setMode('opd')" class="btn {{ $mode==='opd'?'btn-primary':'btn-outline-primary' }}">OPD</button>
            <button type="button" wire:click="setMode('emergency')" class="btn {{ $mode==='emergency'?'btn-danger':'btn-outline-danger' }}">Emergency</button>
            <button type="button" wire:click="setMode('quick')" class="btn {{ $mode==='quick'?'btn-warning':'btn-outline-warning' }}">Quick</button>
        </div>
    </div>

    <div class="wizard-steps">
        <div class="wizard-step {{ $step >= 1 ? ($step===1?'active':'done') : '' }}">1. Patient</div>
        <div class="wizard-step {{ $step >= 2 ? ($step===2?'active':'done') : '' }}">2. Vitals</div>
        <div class="wizard-step {{ $step >= 3 ? 'active' : '' }}">3. Confirm</div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="premium-card p-3 sticky-top consult-sidebar">
                <label class="form-label fw-semibold">Search existing patient</label>
                <input type="text" wire:model.live.debounce.400ms="search" class="form-control" placeholder="Name, mobile, ID...">
                @if(count($searchResults))
                    <div class="list-group list-group-flush mt-2 rounded overflow-hidden">
                        @foreach($searchResults as $p)
                            <button type="button" wire:click="selectPatient({{ $p['id'] }})" class="list-group-item list-group-item-action py-2">
                                <strong>{{ $p['name'] }}</strong><br><small class="text-muted">{{ $p['patient_id'] }}</small>
                            </button>
                        @endforeach
                    </div>
                @endif
                @if($duplicateWarning)
                    <div class="alert alert-warning mt-2 mb-0 py-2 small">
                        <i class="ti ti-alert-triangle"></i> Duplicate mobile — search & select patient
                    </div>
                @endif
                @if($isExisting)
                    <div class="alert alert-info mt-2 mb-0 py-2 small">Returning patient linked</div>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <form wire:submit="register">
                @if($step === 1)
                    <div class="premium-card p-4" wire:transition>
                        <h4 class="mb-3">Patient Information</h4>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label required">Full Name</label>
                                <input type="text" wire:model.blur="name" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label required">Mobile</label>
                                <input type="text" wire:model.live.debounce.500ms="mobile" class="form-control form-control-lg">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Gender</label>
                                <select wire:model="gender" class="form-select form-select-lg">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2"><label class="form-label">Age</label><input type="number" wire:model="age" class="form-control"></div>
                            <div class="col-12 col-md-3"><label class="form-label">Department</label>
                                <select wire:model="department_id" class="form-select">
                                    <option value="">Select</option>
                                    @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4"><label class="form-label">Doctor</label>
                                <select wire:model="doctor_id" class="form-select">
                                    <option value="">Any</option>
                                    @foreach($doctors as $doc)<option value="{{ $doc->id }}">Dr. {{ $doc->user->name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div class="cc-reg-actions text-end mt-3">
                            <button type="button" wire:click="nextStep" class="btn btn-primary btn-lg">Next: Vitals <i class="ti ti-arrow-right"></i></button>
                        </div>
                    </div>
                @elseif($step === 2)
                    <div class="premium-card p-4">
                        <h4 class="mb-3">Vitals & Complaint</h4>
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label">Chief Complaint</label><textarea wire:model="chief_complaint" class="form-control" rows="2"></textarea></div>
                            <div class="col-12 col-md-3"><label class="form-label">BP</label><input wire:model="bp" class="form-control" placeholder="120/80"></div>
                            <div class="col-12 col-md-3"><label class="form-label">Sugar</label><input wire:model="sugar_rbs" class="form-control"></div>
                            <div class="col-12 col-md-2"><label class="form-label">Temp</label><input type="number" step="0.1" wire:model="temperature" class="form-control"></div>
                            <div class="col-12 col-md-2"><label class="form-label">SpO2</label><input type="number" wire:model="spo2" class="form-control"></div>
                            <div class="col-12 col-md-2"><label class="form-label">Weight</label><input type="number" wire:model="weight" class="form-control"></div>
                            <div class="col-12"><label class="form-label">Symptoms</label><textarea wire:model="symptoms" class="form-control" rows="2"></textarea></div>
                        </div>
                        <div class="cc-reg-actions d-flex justify-content-between mt-3">
                            <button type="button" wire:click="prevStep" class="btn btn-outline-secondary">Back</button>
                            <button type="button" wire:click="nextStep" class="btn btn-primary btn-lg">Review & Register</button>
                        </div>
                    </div>
                @else
                    <div class="premium-card p-4">
                        <h4 class="mb-3">Confirm Registration</h4>
                        <div class="row g-2 small">
                            <div class="col-6"><strong>Name:</strong> {{ $name }}</div>
                            <div class="col-6"><strong>Mobile:</strong> {{ $mobile }}</div>
                            <div class="col-6"><strong>Priority:</strong> <span class="badge {{ $priority==='emergency'?'bg-danger':'bg-secondary' }}">{{ ucfirst($priority) }}</span></div>
                            <div class="col-12"><strong>Complaint:</strong> {{ $chief_complaint ?: '—' }}</div>
                        </div>
                        <div class="cc-reg-actions d-flex justify-content-between mt-4">
                            <button type="button" wire:click="prevStep" class="btn btn-outline-secondary">Back</button>
                            <button type="submit" class="btn btn-success btn-lg" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="ti ti-check"></i> Register & Print Token</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if($showPrintModal && $printVisitId)
        <div class="modal show d-block" tabindex="-1" style="background:rgba(15,23,42,0.55);z-index:1060"
             x-data="opdPrintModal()" x-init="init(@js(route('print.opd-slip', ['visit' => $printVisitId, 'embed' => 1])))"
             @keydown.escape.window="$wire.closePrintAndReset()">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden">
                    <div class="modal-header border-0" style="background:var(--cc-primary-light)">
                        <h5 class="modal-title fw-bold"><i class="ti ti-printer me-2"></i>OPD Token Slip</h5>
                        <button type="button" class="btn-close" wire:click="closePrintAndReset"></button>
                    </div>
                    <div class="modal-body p-0 bg-light">
                        <iframe id="opdPrintFrame" src="" style="width:100%;height:420px;border:0" title="OPD Slip"></iframe>
                    </div>
                    <div class="modal-footer border-0 gap-2">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closePrintAndReset">Done — New Registration</button>
                        <button type="button" class="btn btn-primary" @click="printSlip()"><i class="ti ti-printer"></i> Print Again</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));

window.opdPrintModal = () => ({
    printUrl: '',
    init(url) {
        this.printUrl = url;
        const frame = document.getElementById('opdPrintFrame');
        if (!frame) return;
        frame.src = url;
        frame.onload = () => {
            setTimeout(() => {
                try {
                    frame.contentWindow?.focus();
                    frame.contentWindow?.print();
                } catch (e) {
                    window.open(url, '_blank', 'width=420,height=640');
                }
            }, 500);
        };
    },
    printSlip() {
        const frame = document.getElementById('opdPrintFrame');
        try {
            frame?.contentWindow?.print();
        } catch (e) {
            window.open(this.printUrl, '_blank');
        }
    },
});
</script>
@endscript
