<?php

use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\PatientVisit;
use App\Services\ConsultationService;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[On('queue-updated')]
    public function refreshQueue(): void {}

    public function getDoctor(): ?Doctor
    {
        return auth()->user()->doctor;
    }

    public function startConsult(int $visitId): void
    {
        $doctor = $this->getDoctor();
        if (! $doctor) {
            $this->dispatch('notify', title: 'Error', message: 'Doctor profile not found.', type: 'danger');
            return;
        }
        $visit = PatientVisit::findOrFail($visitId);
        app(ConsultationService::class)->startConsultation($visit, $doctor);
        $this->redirect(route('doctor.consult', $visit), navigate: true);
    }

    public function with(): array
    {
        $doctorId = $this->getDoctor()?->id;

        return [
            'waiting' => PatientVisit::with(['patient', 'department'])
                ->where('status', VisitStatus::Waiting)
                ->when($doctorId, fn ($q) => $q->where(fn ($q2) => $q2->where('doctor_id', $doctorId)->orWhereNull('doctor_id')))
                ->whereDate('created_at', today())
                ->orderByRaw("CASE WHEN priority = 'emergency' THEN 0 ELSE 1 END")
                ->orderBy('token_number')->get(),
            'consulting' => PatientVisit::with('patient')
                ->where('status', VisitStatus::WithDoctor)->where('doctor_id', $doctorId)
                ->whereDate('created_at', today())->get(),
            'completedToday' => PatientVisit::where('doctor_id', $doctorId)
                ->where('status', VisitStatus::AtPharmacy)
                ->whereDate('created_at', today())->count(),
        ];
    }
};
?>

<div wire:poll.12s class="doctor-workspace">
    <div class="cc-page-header">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-stethoscope text-primary me-2"></i>Doctor Workspace</h1>
            <p class="cc-page-subtitle"><span class="live-dot"></span> Real-time consultation queue</p>
        </div>
    </div>

    <div class="dw-stats-mini">
        <div class="cc-stat-card">
            <div class="cc-stat-label">Waiting</div>
            <div class="cc-stat-value text-primary">{{ $waiting->count() }}</div>
        </div>
        <div class="cc-stat-card">
            <div class="cc-stat-label">In Consult</div>
            <div class="cc-stat-value text-warning">{{ $consulting->count() }}</div>
        </div>
        <div class="cc-stat-card">
            <div class="cc-stat-label">To Pharmacy</div>
            <div class="cc-stat-value text-success">{{ $completedToday }}</div>
        </div>
    </div>

    <div class="row g-4 dw-panels-row">
        <div class="col-lg-5">
            <div class="premium-card h-100 dw-panel">
                <div class="cc-card-header dw-panel-header">
                    <div class="dw-panel-heading">
                        <span class="dw-panel-icon dw-panel-icon-primary"><i class="ti ti-users"></i></span>
                        <div class="dw-panel-titles">
                            <h3 class="dw-panel-title">Patient Queue</h3>
                            <p class="dw-panel-subtitle">Waiting for consultation today</p>
                        </div>
                    </div>
                    <span class="badge bg-primary-lt dw-panel-badge">{{ $waiting->count() }}</span>
                </div>
                <div class="dw-panel-body">
                    @forelse($waiting as $v)
                        <div class="dw-queue-item {{ $v->isEmergency() ? 'border-danger' : '' }}" wire:key="w-{{ $v->id }}">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="queue-token" style="font-size:1.5rem">#{{ $v->token_number }}</span>
                                        @if($v->isEmergency())<span class="badge bg-danger badge-emergency">ER</span>@endif
                                    </div>
                                    <div class="fw-bold mt-1">{{ $v->patient->name }}</div>
                                    <div class="small text-muted">{{ $v->patient->age }}y · {{ ucfirst($v->patient->gender ?? '-') }}</div>
                                    <div class="small mt-1 text-truncate">{{ $v->chief_complaint }}</div>
                                    @if($v->bp || $v->spo2)
                                        <div class="d-flex gap-2 mt-2 small">
                                            @if($v->bp)<span class="badge bg-secondary-lt">BP {{ $v->bp }}</span>@endif
                                            @if($v->spo2)<span class="badge bg-secondary-lt">SpO2 {{ $v->spo2 }}%</span>@endif
                                        </div>
                                    @endif
                                </div>
                                <button wire:click="startConsult({{ $v->id }})" class="btn btn-primary btn-sm flex-shrink-0" wire:loading.attr="disabled">
                                    <i class="ti ti-player-play"></i> Start
                                </button>
                            </div>
                        </div>
                    @empty
                        <x-ui.empty-state icon="ti-mood-smile" title="Queue empty" message="No patients waiting for consultation" />
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7 d-flex flex-column gap-3">
            <div class="premium-card dw-panel flex-fill">
                <div class="cc-card-header dw-panel-header">
                    <div class="dw-panel-heading">
                        <span class="dw-panel-icon dw-panel-icon-warning"><i class="ti ti-stethoscope"></i></span>
                        <div class="dw-panel-titles">
                            <h3 class="dw-panel-title">Active Consultations</h3>
                            <p class="dw-panel-subtitle">Patients you are currently seeing</p>
                        </div>
                    </div>
                    <span class="badge bg-warning-lt dw-panel-badge">{{ $consulting->count() }}</span>
                </div>
                <div class="dw-panel-body dw-panel-body-compact">
                    @forelse($consulting as $v)
                        <a href="{{ route('doctor.consult', $v) }}" class="btn btn-warning btn-lg w-100 mb-2 d-flex justify-content-between align-items-center">
                            <span><i class="ti ti-arrow-right me-2"></i>{{ $v->patient->name }}</span>
                            <span class="badge bg-dark">#{{ $v->token_number }}</span>
                        </a>
                    @empty
                        <div class="cc-empty py-4">
                            <i class="ti ti-armchair"></i>
                            <p class="mb-0">Select a patient from the queue to begin consultation</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="premium-card dw-panel">
                <div class="cc-card-header dw-panel-header dw-panel-header-tips">
                    <div class="dw-panel-heading">
                        <span class="dw-panel-icon dw-panel-icon-info"><i class="ti ti-bulb"></i></span>
                        <div class="dw-panel-titles">
                            <h3 class="dw-panel-title">Quick Tips</h3>
                            <p class="dw-panel-subtitle">Shortcuts &amp; workflow hints</p>
                        </div>
                    </div>
                </div>
                <div class="dw-panel-body dw-panel-body-tips">
                    <ul class="dw-tips-list mb-0">
                        <li><kbd>Ctrl+S</kbd> Save consultation on consult screen</li>
                        <li>Queue updates automatically via WebSocket</li>
                        <li>Emergency patients appear at the top</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
