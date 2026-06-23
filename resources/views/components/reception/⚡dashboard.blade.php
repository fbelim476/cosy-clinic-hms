<?php

use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Services\PatientService;
use App\Services\QueueService;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public bool $loading = false;
    public ?int $transferVisitId = null;
    public ?int $transferDoctorId = null;

    #[On('queue-updated')]
    public function refreshQueue(): void
    {
        $this->dispatch('$refresh');
    }

    public function sendToDoctor(int $visitId): void
    {
        $visit = PatientVisit::findOrFail($visitId);
        app(PatientService::class)->sendToDoctor($visit);
        $this->dispatch('notify', title: 'Sent to Doctor', message: "Token {$visit->displayToken()} is now in doctor queue.", type: 'success');
    }

    public function recallToken(int $visitId): void
    {
        $visit = app(QueueService::class)->recallToken(PatientVisit::findOrFail($visitId));
        $this->dispatch('notify', title: 'Recalled', message: "Token {$visit->displayToken()} recalled.", type: 'info');
    }

    public function cancelToken(int $visitId): void
    {
        $visit = app(QueueService::class)->cancelToken(PatientVisit::findOrFail($visitId));
        $this->dispatch('notify', title: 'Cancelled', message: "Token {$visit->displayToken()} cancelled.", type: 'warning');
    }

    public function openTransfer(int $visitId): void
    {
        $this->transferVisitId = $visitId;
        $this->transferDoctorId = null;
    }

    public function transferQueue(): void
    {
        $this->validate([
            'transferVisitId' => 'required|exists:patient_visits,id',
            'transferDoctorId' => 'required|exists:doctors,id',
        ]);

        $visit = app(QueueService::class)->transferQueue(
            PatientVisit::findOrFail($this->transferVisitId),
            $this->transferDoctorId
        );

        $this->transferVisitId = null;
        $this->transferDoctorId = null;
        $this->dispatch('notify', title: 'Transferred', message: "Patient moved — new token {$visit->displayToken()}", type: 'success');
    }

    public function with(): array
    {
        $today = PatientVisit::whereDate('created_at', today());
        $branchId = auth()->user()->branch_id ?? 1;

        return [
            'todayCount' => (clone $today)->count(),
            'emergencyCount' => (clone $today)->where('priority', 'emergency')->where('status', '!=', VisitStatus::Completed->value)->count(),
            'totalPatients' => Patient::count(),
            'doctorBoard' => app(QueueService::class)->doctorBoard($branchId),
            'allDoctors' => Doctor::with('user')->where('is_available', true)->orderBy('id')->get(),
        ];
    }
};
?>

<div wire:poll.15s>
    <div class="cc-page-header">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-layout-list text-primary me-2"></i>Reception Queue Board</h1>
            <p class="cc-page-subtitle"><span class="live-dot"></span> All doctor queues — real-time multi-doctor OPD</p>
        </div>
        <a href="{{ route('reception.register') }}" class="btn btn-cc-primary btn-lg">
            <i class="ti ti-user-plus me-1"></i> Register Patient
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="stat-gradient p-3 interactive"><div class="small opacity-75">Today's OPD</div><div class="stat-value">{{ $todayCount }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-soft"><div class="stat-label">Doctors Active</div><div class="stat-value">{{ $doctorBoard->count() }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-soft"><div class="stat-label">Emergency</div><div class="stat-value text-danger">{{ $emergencyCount }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-soft"><div class="stat-label">All Patients</div><div class="stat-value">{{ $totalPatients }}</div></div></div>
    </div>

    <div class="row g-4">
        @foreach($doctorBoard as $board)
            @php $doctor = $board['doctor']; @endphp
            <div class="col-12 col-xl-6" wire:key="board-{{ $doctor->id }}">
                <div class="premium-card h-100">
                    <div class="cc-card-header">
                        <div>
                            <h3 class="h5 mb-0 fw-bold">{{ $doctor->user->name }}</h3>
                            <div class="small text-muted">{{ $doctor->department?->name }} @if($doctor->room_number)· Room {{ $doctor->room_number }}@endif</div>
                        </div>
                        <span class="badge bg-primary-lt">{{ $board['waiting'] }} waiting</span>
                    </div>
                    <div class="cc-card-body pt-0">
                        <div class="row g-2 mb-3 small">
                            <div class="col-4"><strong>Now Serving</strong><br><span class="fs-4 fw-bold text-primary">{{ $board['current_token'] ?? '—' }}</span></div>
                            <div class="col-4"><strong>Waiting</strong><br>{{ $board['waiting'] }}</div>
                            <div class="col-4"><strong>Completed</strong><br>{{ $board['completed'] }}</div>
                        </div>

                        @forelse($board['waiting_list'] as $v)
                            <div class="border rounded-3 p-2 mb-2 {{ $v->isEmergency() ? 'border-danger' : '' }}" wire:key="doc-{{ $doctor->id }}-visit-{{ $v->id }}">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-bold">{{ $v->displayToken() }} — {{ $v->patient->name }}</div>
                                        <div class="small text-muted">{{ $v->patient->mobile }}</div>
                                    </div>
                                    <span class="badge {{ $v->status->badgeClass() }}">{{ $v->status->label() }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    @if($v->status->value === 'registered')
                                        <button wire:click="sendToDoctor({{ $v->id }})" class="btn btn-primary btn-sm">Send to Doctor</button>
                                    @endif
                                    <button wire:click="recallToken({{ $v->id }})" class="btn btn-outline-secondary btn-sm">Recall</button>
                                    <button wire:click="openTransfer({{ $v->id }})" class="btn btn-outline-info btn-sm">Transfer</button>
                                    <button wire:click="cancelToken({{ $v->id }})" class="btn btn-outline-danger btn-sm">Cancel</button>
                                    <a href="{{ route('print.opd-slip', $v) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="ti ti-printer"></i></a>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small py-2">No patients in this doctor's queue.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($transferVisitId)
        <div class="modal show d-block" style="background:rgba(15,23,42,0.5);z-index:1060">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius:16px">
                    <div class="modal-header"><h5 class="modal-title">Transfer to Another Doctor</h5>
                        <button type="button" class="btn-close" wire:click="$set('transferVisitId', null)"></button></div>
                    <div class="modal-body">
                        <label class="form-label">Select Doctor</label>
                        <select wire:model="transferDoctorId" class="form-select">
                            <option value="">Choose doctor</option>
                            @foreach($allDoctors as $d)<option value="{{ $d->id }}">{{ $d->user->name }} ({{ $d->department?->name }})</option>@endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('transferVisitId', null)">Cancel</button>
                        <button class="btn btn-primary" wire:click="transferQueue">Transfer & Re-issue Token</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
