<?php

use App\Enums\VisitStatus;
use App\Models\PatientVisit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts.display')] class extends Component
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
                ->orderBy('token_number')->limit(6)->get(),
            'waitingCount' => PatientVisit::where('status', VisitStatus::Waiting)->whereDate('created_at', today())->count(),
        ];
    }
};
?>

<div wire:poll.3s class="token-display-premium" x-data x-init="if(window.Echo){Echo.channel('cliniccare-queue').listen('.visit-queue-updated',()=>$wire.$refresh())}">
    <div class="row g-5 align-items-center text-center">
        <div class="col-lg-6">
            <div class="display-label">NOW SERVING</div>
            <div class="display-token-hero" wire:transition>{{ $current?->token_number ?? '—' }}</div>
            <div class="display-patient-name">{{ $current?->patient?->name ?? 'Please wait' }}</div>
        </div>
        <div class="col-lg-6">
            <div class="display-label">WAITING — {{ $waitingCount }} patients</div>
            <div class="row g-3 mt-2 justify-content-center">
                @foreach($next as $v)
                    <div class="col-4 col-md-3">
                        <div class="next-token-box {{ $v->isEmergency() ? 'emergency' : '' }}" wire:key="t-{{ $v->id }}">
                            <div class="next-num">#{{ $v->token_number }}</div>
                            <div class="next-name">{{ Str::limit($v->patient->name, 12) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
