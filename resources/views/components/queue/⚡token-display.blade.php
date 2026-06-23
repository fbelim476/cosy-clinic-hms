<?php

use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\PatientVisit;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[On('queue-updated')]
    public function refreshDisplay(): void {}

    public function with(): array
    {
        $doctors = Doctor::with(['user', 'department'])
            ->where('is_available', true)
            ->orderBy('department_id')
            ->get();

        $panels = $doctors->map(function (Doctor $doctor) {
            $base = PatientVisit::where('doctor_id', $doctor->id)->whereDate('created_at', today());

            $current = (clone $base)
                ->where('status', VisitStatus::WithDoctor)
                ->latest('consultation_started_at')
                ->with('patient')
                ->first();

            $nextWaiting = (clone $base)
                ->where('status', VisitStatus::Waiting)
                ->with('patient')
                ->orderByRaw("CASE WHEN priority = 'emergency' THEN 0 ELSE 1 END")
                ->orderBy('token_number')
                ->first();

            $docName = $doctor->user->name;

            return [
                'doctor' => $doctor,
                'doctor_name' => str_starts_with($docName, 'Dr.') ? $docName : 'Dr. '.$docName,
                'current' => $current,
                'current_token' => $current?->displayToken() ?? '—',
                'current_patient' => $current?->patient?->name ?? 'Please wait',
                'is_emergency' => $current?->isEmergency() ?? false,
                'waiting' => (clone $base)->where('status', VisitStatus::Waiting)->count(),
                'next_token' => $nextWaiting?->displayToken(),
                'next_patient' => $nextWaiting?->patient?->name,
            ];
        });

        return [
            'panels' => $panels,
            'panelCount' => max($panels->count(), 1),
        ];
    }
};
?>

<div
    wire:poll.5s
    class="token-display-body td-multi-body"
    x-data
    x-init="if (window.Echo) { Echo.channel('CosyClinic-queue').listen('.visit-queue-updated', () => $wire.$refresh()) }"
>
    <div class="td-multi-grid" data-count="{{ $panelCount }}">
        @forelse($panels as $panel)
            <section
                class="td-doctor-panel {{ $panel['is_emergency'] ? 'td-panel-emergency' : '' }} {{ $panel['current'] ? 'td-panel-active' : 'td-panel-idle' }}"
                wire:key="td-doc-{{ $panel['doctor']->id }}"
            >
                <div class="td-panel-top">
                    <div class="td-doctor-avatar"><i class="ti ti-stethoscope"></i></div>
                    <div class="td-panel-meta">
                        <div class="td-doctor-name">{{ $panel['doctor_name'] }}</div>
                        <div class="td-doctor-dept">{{ $panel['doctor']->department?->name ?? 'OPD' }}</div>
                        @if($panel['doctor']->room_number)
                            <div class="td-doctor-room"><i class="ti ti-door"></i> Room {{ $panel['doctor']->room_number }}</div>
                        @endif
                    </div>
                </div>

                <div class="td-doctor-label">Now Serving</div>
                <div class="td-doctor-token" wire:key="token-{{ $panel['doctor']->id }}-{{ $panel['current_token'] }}">
                    {{ $panel['current_token'] }}
                </div>
                <div class="td-doctor-patient">{{ $panel['current_patient'] }}</div>

                <div class="td-panel-footer">
                    <div class="td-doctor-waiting">
                        <span class="td-wait-label">Waiting</span>
                        <strong>{{ $panel['waiting'] }}</strong>
                    </div>
                    @if($panel['next_token'])
                        <div class="td-next-inline">
                            <span class="td-next-inline-label">Up Next</span>
                            <span class="td-next-inline-token">{{ $panel['next_token'] }}</span>
                            <span class="td-next-inline-name">{{ $panel['next_patient'] }}</span>
                        </div>
                    @endif
                </div>
            </section>
        @empty
            <section class="td-doctor-panel td-doctor-panel-empty">
                <div class="td-doctor-label">No active doctors</div>
                <div class="td-doctor-token">—</div>
                <p class="td-idle-msg">Configure doctors in Admin panel</p>
            </section>
        @endforelse
    </div>
</div>
