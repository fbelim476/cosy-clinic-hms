<?php

use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\PatientVisit;
use App\Services\ConsultationService;
use App\Services\QueueService;
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
        $visit = PatientVisit::where('doctor_id', $doctor->id)->findOrFail($visitId);
        app(ConsultationService::class)->startConsultation($visit, $doctor);
        $this->redirect(route('doctor.consult', $visit), navigate: true);
    }

    public function callNext(): void
    {
        $doctor = $this->getDoctor();
        if (! $doctor) return;

        $next = PatientVisit::with('patient')
            ->where('doctor_id', $doctor->id)
            ->where('status', VisitStatus::Waiting)
            ->whereDate('created_at', today())
            ->orderByRaw("CASE WHEN priority = 'emergency' THEN 0 ELSE 1 END")
            ->orderBy('token_number')
            ->first();

        if (! $next) {
            $this->dispatch('notify', title: 'Queue Empty', message: 'No waiting patients in your queue.', type: 'info');
            return;
        }

        app(ConsultationService::class)->startConsultation($next, $doctor);
        $this->redirect(route('doctor.consult', $next), navigate: true);
    }

    public function with(): array
    {
        $doctor = $this->getDoctor();
        $doctorId = $doctor?->id;
        $stats = $doctor ? app(QueueService::class)->doctorStats($doctor) : [
            'waiting' => 0, 'in_consult' => 0, 'completed' => 0, 'revenue' => 0, 'avg_minutes' => 0,
        ];

        return [
            'doctor' => $doctor,
            'stats' => $stats,
            'waiting' => PatientVisit::with(['patient', 'department'])
                ->where('status', VisitStatus::Waiting)
                ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
                ->whereDate('created_at', today())
                ->orderByRaw("CASE WHEN priority = 'emergency' THEN 0 ELSE 1 END")
                ->orderBy('token_number')->get(),
            'consulting' => PatientVisit::with('patient')
                ->where('status', VisitStatus::WithDoctor)->where('doctor_id', $doctorId)
                ->whereDate('created_at', today())->get(),
        ];
    }
};
?>

<div wire:poll.12s class="doctor-workspace">
    <div class="cc-page-header">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-stethoscope text-primary me-2"></i>{{ $doctor?->user->name ?? 'Doctor Workspace' }}</h1>
            <p class="cc-page-subtitle"><span class="live-dot"></span> {{ $doctor?->department?->name ?? 'Consultation' }} · Only your assigned patients</p>
        </div>
        @if($doctor)
            <button wire:click="callNext" class="btn btn-primary btn-lg"><i class="ti ti-player-play"></i> Call Next Patient</button>
        @endif
    </div>

    <div class="dw-stats-mini">
        <div class="cc-stat-card"><div class="cc-stat-label">Waiting</div><div class="cc-stat-value text-primary">{{ $stats['waiting'] }}</div></div>
        <div class="cc-stat-card"><div class="cc-stat-label">In Consult</div><div class="cc-stat-value text-warning">{{ $stats['in_consult'] }}</div></div>
        <div class="cc-stat-card"><div class="cc-stat-label">Completed</div><div class="cc-stat-value text-success">{{ $stats['completed'] }}</div></div>
        <div class="cc-stat-card"><div class="cc-stat-label">Revenue Today</div><div class="cc-stat-value">₹{{ number_format($stats['revenue'], 0) }}</div></div>
        <div class="cc-stat-card"><div class="cc-stat-label">Avg Time</div><div class="cc-stat-value">{{ $stats['avg_minutes'] }}m</div></div>
    </div>

    <div class="row g-4 dw-panels-row">
        <div class="col-lg-5">
            <div class="premium-card h-100 dw-panel">
                <div class="cc-card-header dw-panel-header">
                    <div class="dw-panel-heading">
                        <span class="dw-panel-icon dw-panel-icon-primary"><i class="ti ti-users"></i></span>
                        <div class="dw-panel-titles">
                            <h3 class="dw-panel-title">My Queue</h3>
                            <p class="dw-panel-subtitle">Patients assigned to you today</p>
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
                                        <span class="queue-token" style="font-size:1.5rem">{{ $v->displayToken() }}</span>
                                        @if($v->isEmergency())<span class="badge bg-danger badge-emergency">ER</span>@endif
                                    </div>
                                    <div class="fw-bold mt-1">{{ $v->patient->name }}</div>
                                    <div class="small text-muted">{{ $v->patient->age }}y · {{ ucfirst($v->patient->gender ?? '-') }}</div>
                                    <div class="small mt-1 text-truncate">{{ $v->chief_complaint }}</div>
                                </div>
                                <button wire:click="startConsult({{ $v->id }})" class="btn btn-primary btn-sm flex-shrink-0" wire:loading.attr="disabled">
                                    <i class="ti ti-player-play"></i> Start
                                </button>
                            </div>
                        </div>
                    @empty
                        <x-ui.empty-state icon="ti-mood-smile" title="Queue empty" message="No patients waiting in your queue" />
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="premium-card h-100 dw-panel">
                <div class="cc-card-header dw-panel-header">
                    <div class="dw-panel-heading">
                        <span class="dw-panel-icon dw-panel-icon-warning"><i class="ti ti-armchair"></i></span>
                        <div class="dw-panel-titles">
                            <h3 class="dw-panel-title">Active Consultations</h3>
                            <p class="dw-panel-subtitle">Patients you are currently seeing</p>
                        </div>
                    </div>
                    <span class="badge bg-warning-lt dw-panel-badge">{{ $consulting->count() }}</span>
                </div>
                <div class="dw-panel-body dw-panel-body-compact">
                    @forelse($consulting as $v)
                        <div class="d-flex justify-content-between align-items-center border rounded-3 p-3 mb-2" wire:key="c-{{ $v->id }}">
                            <div>
                                <span class="badge bg-dark">{{ $v->displayToken() }}</span>
                                <span class="fw-bold ms-2">{{ $v->patient->name }}</span>
                            </div>
                            <a href="{{ route('doctor.consult', $v) }}" class="btn btn-warning btn-sm">Continue</a>
                        </div>
                    @empty
                        <x-ui.empty-state icon="ti-armchair" title="No active consult" message="Select a patient from your queue to begin" />
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
