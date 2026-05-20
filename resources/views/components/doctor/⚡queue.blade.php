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
        ];
    }
};
?>

<div wire:poll.12s>
    <div class="mb-4">
        <h2 class="h3 fw-bold">Doctor Workspace</h2>
        <span class="text-muted small"><span class="live-dot"></span> Queue syncs in real-time</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="premium-card border-primary border-2">
                <div class="card-header bg-primary-lt border-0">
                    <h3 class="card-title text-primary mb-0">Waiting ({{ $waiting->count() }})</h3>
                </div>
                <div class="list-group list-group-flush" style="max-height:65vh;overflow-y:auto">
                    @forelse($waiting as $v)
                        <div class="list-group-item token-card border-0 rounded-0 {{ $v->isEmergency() ? 'emergency' : '' }}" wire:key="w-{{ $v->id }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="token-hero" style="font-size:1.75rem">#{{ $v->token_number }}</span>
                                    <div class="fw-bold">{{ $v->patient->name }}</div>
                                    <div class="small text-muted">{{ Str::limit($v->chief_complaint, 50) }}</div>
                                </div>
                                <button wire:click="startConsult({{ $v->id }})" class="btn btn-primary">
                                    <i class="ti ti-player-play"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No patients waiting</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="premium-card p-4">
                <h4 class="mb-3"><i class="ti ti-stethoscope me-2"></i>Currently Consulting</h4>
                @forelse($consulting as $v)
                    <a href="{{ route('doctor.consult', $v) }}" class="btn btn-warning btn-lg w-100 mb-2">
                        Continue — {{ $v->patient->name }} (Token #{{ $v->token_number }})
                    </a>
                @empty
                    <p class="text-muted mb-0">Select a patient from the queue to begin</p>
                @endforelse
            </div>
            <div class="mt-3 small text-muted">
                <kbd>F2</kbd> Focus search · <kbd>Ctrl+S</kbd> Save consultation (on consult screen)
            </div>
        </div>
    </div>
</div>
