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

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 mb-0 fw-bold">Reception Queue</h2>
            <span class="text-muted small"><span class="live-dot"></span> Real-time updates active</span>
        </div>
        <a href="{{ route('reception.register') }}" class="btn btn-primary">
            <i class="ti ti-user-plus me-1"></i> Quick Register
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-gradient premium-card p-3 interactive">
                <div class="small opacity-75">Today's OPD</div>
                <div class="stat-value">{{ $todayCount }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-soft premium-card p-3 interactive">
                <div class="text-muted small">Waiting</div>
                <div class="fs-2 fw-bold text-primary">{{ $waiting->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-soft premium-card p-3 interactive">
                <div class="text-muted small">Emergency</div>
                <div class="fs-2 fw-bold text-danger">{{ $emergencyCount }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-soft premium-card p-3 interactive">
                <div class="text-muted small">Total Patients</div>
                <div class="fs-2 fw-bold">{{ $totalPatients }}</div>
            </div>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center py-3">
            <h3 class="card-title mb-0"><i class="ti ti-list-numbers me-2"></i>Live Queue Board</h3>
            <span class="badge bg-primary-lt">{{ $waiting->count() }} waiting</span>
        </div>
        <div class="card-body pt-0">
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
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="ti ti-mood-empty" style="font-size:3rem;opacity:.3"></i>
                        <p class="mt-2 mb-0">No patients in queue — register a new OPD patient</p>
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
