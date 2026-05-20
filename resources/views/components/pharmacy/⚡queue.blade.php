<?php

use App\Enums\VisitStatus;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Services\PharmacyService;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?int $activeOrderId = null;
    public string $tab = 'pending';

    #[On('queue-updated')]
    public function refreshQueue(): void {}

    public function processVisit(int $visitId): void
    {
        $visit = PatientVisit::findOrFail($visitId);
        $order = app(PharmacyService::class)->createOrderFromVisit($visit, auth()->id());
        $this->activeOrderId = $order->id;
        $this->tab = 'processing';
    }

    public function completeOrder(int $orderId): void
    {
        app(PharmacyService::class)->completeOrder(PharmacyOrder::findOrFail($orderId));
        $this->activeOrderId = null;
        $this->tab = 'pending';
        $this->dispatch('notify', title: 'Completed', message: 'Order billed and sent to accounts.', type: 'success');
    }

    public function with(): array
    {
        return [
            'pending' => PatientVisit::with(['patient', 'prescriptions.items'])
                ->where('status', VisitStatus::AtPharmacy)
                ->whereDate('created_at', today())
                ->orderBy('token_number')->get(),
            'activeOrder' => $this->activeOrderId
                ? PharmacyOrder::with(['items', 'patientVisit.patient', 'prescription', 'patientVisit.consultation'])->find($this->activeOrderId)
                : null,
            'completedToday' => PharmacyOrder::with('patientVisit.patient')
                ->where('status', 'completed')->whereDate('created_at', today())->count(),
        ];
    }
};
?>

<div wire:poll.12s class="pharmacy-pos">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 fw-bold mb-0">Pharmacy POS</h2>
            <span class="text-muted small"><span class="live-dot"></span> {{ $pending->count() }} pending · {{ $completedToday }} completed today</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="premium-card">
                <div class="card-header border-0">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item"><button type="button" class="nav-link {{ $tab==='pending'?'active':'' }}" wire:click="$set('tab','pending')">Pending</button></li>
                        <li class="nav-item"><button type="button" class="nav-link {{ $tab==='processing'?'active':'' }}" wire:click="$set('tab','processing')">Active</button></li>
                    </ul>
                </div>
                <div class="list-group list-group-flush" style="max-height:70vh;overflow-y:auto">
                    @forelse($pending as $v)
                        <button type="button" wire:click="processVisit({{ $v->id }})"
                                class="list-group-item list-group-item-action py-3 {{ $activeOrder && $activeOrder->patient_visit_id === $v->id ? 'active' : '' }}">
                            <div class="d-flex justify-content-between">
                                <span class="token-hero" style="font-size:1.5rem">#{{ $v->token_number }}</span>
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <strong>{{ $v->patient->name }}</strong>
                        </button>
                    @empty
                        <div class="p-4 text-muted text-center small">No pending prescriptions</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if($activeOrder)
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="premium-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">{{ $activeOrder->patientVisit->patient->name }}</h4>
                                    <small class="text-muted">{{ $activeOrder->order_number }}</small>
                                </div>
                            </div>
                            @if($activeOrder->patientVisit->consultation?->show_diagnosis_to_pharmacy && $activeOrder->patientVisit->consultation?->diagnosis)
                                <div class="px-3 py-2 bg-azure-lt small"><strong>Diagnosis:</strong> {{ $activeOrder->patientVisit->consultation->diagnosis }}</div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover mb-0">
                                    <thead class="table-light"><tr><th>Medicine</th><th>Qty</th><th>Rate</th><th>GST</th><th class="text-end">Total</th></tr></thead>
                                    <tbody>
                                        @foreach($activeOrder->items as $item)
                                            <tr style="font-size:1.05rem">
                                                <td class="fw-semibold">{{ $item->medicine_name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->gst_percent }}%</td>
                                                <td class="text-end fw-bold">₹{{ number_format($item->total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="pos-panel p-4">
                            <h5 class="fw-bold mb-3">Bill Summary</h5>
                            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>₹{{ number_format($activeOrder->subtotal, 2) }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span>Tax</span><span>₹{{ number_format($activeOrder->tax, 2) }}</span></div>
                            <hr>
                            <div class="d-flex justify-content-between fs-3 fw-bold text-primary mb-4">
                                <span>Total</span><span>₹{{ number_format($activeOrder->total, 2) }}</span>
                            </div>
                            <a href="{{ route('print.pharmacy-invoice', $activeOrder) }}" target="_blank" class="btn btn-outline-primary w-100 mb-2 btn-lg">
                                <i class="ti ti-printer"></i> Print
                            </a>
                            <button wire:click="completeOrder({{ $activeOrder->id }})" class="btn btn-success w-100 btn-lg" wire:loading.attr="disabled">
                                <i class="ti ti-check"></i> Complete & Bill
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="premium-card p-5 text-center text-muted">
                    <i class="ti ti-vaccine-bottle" style="font-size:4rem;opacity:.2"></i>
                    <p class="mt-3 mb-0 fs-5">Select a patient to start dispensing</p>
                    <p class="small">Use barcode scanner or click from pending list</p>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
