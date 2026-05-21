<?php

use App\Enums\VisitStatus;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Services\PatientService;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public bool $loading = false;

    #[On('queue-updated')]
    public function refreshQueue(): void
    {
        $this->dispatch('$refresh');
    }

    public function sendToDoctor(int $visitId): void
    {
        $visit = PatientVisit::findOrFail($visitId);
        app(PatientService::class)->sendToDoctor($visit);
        $this->dispatch('notify', title: 'Sent to Doctor', message: "Token #{$visit->token_number} is now in doctor queue.", type: 'success');
    }

    public function with(): array
    {
        $today = PatientVisit::whereDate('created_at', today());

        return [
            'todayCount' => (clone $today)->count(),
            'waiting' => PatientVisit::with(['patient', 'doctor.user'])
                ->whereIn('status', [VisitStatus::Registered, VisitStatus::Waiting])
                ->whereDate('created_at', today())
                ->orderByRaw("CASE WHEN priority = 'emergency' THEN 0 ELSE 1 END")
                ->orderBy('token_number')
                ->get(),
            'emergencyCount' => (clone $today)->where('priority', 'emergency')->where('status', '!=', VisitStatus::Completed->value)->count(),
            'totalPatients' => Patient::count(),
        ];
    }
};
?>

<div wire:poll.15s>

    <div class="cc-page-header">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-layout-list text-primary me-2"></i>Reception Queue</h1>
            <p class="cc-page-subtitle"><span class="live-dot"></span> Real-time OPD queue — no refresh needed</p>
        </div>
        <a href="{{ route('reception.register') }}" class="btn btn-cc-primary btn-lg">
            <i class="ti ti-user-plus me-1"></i> Register Patient
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-gradient p-3 interactive"><div class="small opacity-75">Today's OPD</div><div class="stat-value">{{ $todayCount }}</div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-soft"><div class="stat-label">Waiting</div><div class="stat-value">{{ $waiting->count() }}</div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-soft"><div class="stat-label">Emergency</div><div class="stat-value text-danger">{{ $emergencyCount }}</div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-soft"><div class="stat-label">All Patients</div><div class="stat-value">{{ $totalPatients }}</div></div>
        </div>
    </div>

    <div class="cc-card premium-card">
        <div class="cc-card-header">
            <h3 class="h5 mb-0 fw-bold"><i class="ti ti-list-numbers me-2"></i>Live Queue Board</h3>
            <span class="badge bg-primary-lt px-3 py-2">{{ $waiting->count() }} waiting</span>
        </div>
        <div class="cc-card-body pt-0">
            <div class="row g-3" wire:loading.class="opacity-50">
                @forelse($waiting as $v)
                    <div class="col-md-6 col-xl-4" wire:key="visit-{{ $v->id }}">
                        <div class="token-card {{ $v->isEmergency() ? 'emergency' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="token-hero">#{{ $v->token_number }}</div>
                                    <div class="fw-bold mt-1">{{ $v->patient->name }}</div>
                                    <div class="small text-muted">{{ $v->patient->mobile }} · {{ $v->patient->patient_id }}</div>
                                    @if($v->chief_complaint)
                                        <div class="small mt-2 text-truncate">{{ $v->chief_complaint }}</div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if($v->isEmergency())
                                        <span class="badge bg-danger">EMERGENCY</span>
                                    @endif
                                    <span class="badge {{ $v->status->badgeClass() }} mt-1">{{ $v->status->label() }}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <button wire:click="sendToDoctor({{ $v->id }})" wire:loading.attr="disabled"
                                        class="btn btn-primary btn-sm flex-fill">
                                    <i class="ti ti-send"></i> Send to Doctor
                                </button>
                                <a href="{{ route('print.opd-slip', $v) }}" target="_blank" class="btn btn-outline-secondary btn-sm btn-icon">
                                    <i class="ti ti-printer"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="cc-empty">
                            <i class="ti ti-mood-empty"></i>
                            <p class="fw-semibold">Queue is empty</p>
                            <p class="small">Register a new OPD patient to get started</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));
</script>
@endscript
