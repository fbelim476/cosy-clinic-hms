<?php

use App\Enums\VisitStatus;
use App\Models\PatientVisit;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[On('queue-updated')]
    public function refreshDisplay(): void {}

    public function with(): array
    {
        return [
            'current' => PatientVisit::with('patient')
                ->where('status', VisitStatus::WithDoctor)
                ->whereDate('created_at', today())
                ->latest('consultation_started_at')->first(),
            'next' => PatientVisit::with('patient')
                ->where('status', VisitStatus::Waiting)
                ->whereDate('created_at', today())
                ->orderByRaw("CASE WHEN priority = 'emergency' THEN 0 ELSE 1 END")
                ->orderBy('token_number')->limit(8)->get(),
            'waitingCount' => PatientVisit::where('status', VisitStatus::Waiting)->whereDate('created_at', today())->count(),
        ];
    }
};
?>

<div
    wire:poll.3s
    class="token-display-body"
    x-data
    x-init="if (window.Echo) { Echo.channel('CosyClinic-queue').listen('.visit-queue-updated', () => $wire.$refresh()) }"
>
    <div class="td-layout">
        {{-- NOW SERVING --}}
        <section class="td-now-panel" aria-label="Now serving">
            <div class="td-now-label">Now Serving</div>
            <div class="td-token" wire:key="current-token-{{ $current?->id ?? 0 }}">
                {{ $current?->token_number ?? '—' }}
            </div>
            <div class="td-patient" wire:key="current-name-{{ $current?->id ?? 0 }}">
                {{ $current?->patient?->name ?? 'Please wait' }}
            </div>
            @if($current?->isEmergency())
                <span class="td-emergency-badge" wire:key="current-er-{{ $current->id }}">Emergency</span>
            @endif
        </section>

        {{-- UP NEXT --}}
        <aside class="td-next-panel" aria-label="Up next">
            <div class="td-next-title">
                Up Next — <span class="td-next-count">{{ $waitingCount }}</span> waiting
            </div>
            <div class="td-next-grid">
                @forelse($next as $v)
                    <div
                        class="td-next {{ $v->isEmergency() ? 'emergency' : '' }}"
                        wire:key="next-{{ $v->id }}"
                    >
                        <div class="td-next-num">#{{ $v->token_number }}</div>
                        <div class="td-next-name">{{ $v->patient->name }}</div>
                    </div>
                @empty
                    <div class="td-next td-next-empty">
                        <div class="td-next-num">—</div>
                        <div class="td-next-name">No patients in queue</div>
                    </div>
                @endforelse
            </div>
        </aside>
    </div>
</div>
