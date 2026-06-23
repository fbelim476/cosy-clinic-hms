<?php

use App\Enums\VisitStatus;
use App\Models\Medicine;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Models\PharmacyOrderItem;
use App\Services\PharmacyService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?int $activeOrderId = null;
    public string $tab = 'pending';
    public string $medicineSearch = '';
    public string $barcodeInput = '';
    public float $orderDiscount = 0;
    public bool $showPayment = false;
    public float $paidAmount = 0;
    public string $paymentMethod = 'cash';

    #[On('queue-updated')]
    public function refreshQueue(): void {}

    public function processVisit(int $visitId): void
    {
        $visit = PatientVisit::findOrFail($visitId);
        $order = app(PharmacyService::class)->createOrderFromVisit($visit, auth()->id());
        $this->activeOrderId = $order->id;
        $this->orderDiscount = (float) $order->discount;
        $this->paidAmount = (float) $order->total;
        $this->tab = 'processing';
        $this->showPayment = false;
    }

    public function selectOrder(int $orderId): void
    {
        $order = PharmacyOrder::findOrFail($orderId);
        $this->activeOrderId = $order->id;
        $this->orderDiscount = (float) $order->discount;
        $this->paidAmount = (float) $order->total;
        $this->tab = 'processing';
    }

    public function updatedMedicineSearch(): void {}

    public function scanBarcode(): void
    {
        if (! $this->barcodeInput || ! $this->activeOrderId) {
            return;
        }
        $med = Medicine::where('is_active', true)
            ->where(fn ($q) => $q->where('barcode', $this->barcodeInput)->orWhere('sku', $this->barcodeInput))
            ->first();
        if ($med) {
            $this->addMedicine($med->id);
        }
        $this->barcodeInput = '';
    }

    public function addMedicine(int $medicineId): void
    {
        if (! $this->activeOrderId) {
            return;
        }
        $med = Medicine::findOrFail($medicineId);
        $order = PharmacyOrder::findOrFail($this->activeOrderId);
        app(PharmacyService::class)->addOtcFromMedicine($order, $med, 1);
        $this->medicineSearch = '';
        $this->refreshOrderTotals();
        unset($this->activeOrder);
    }

    public function refreshOrderTotals(): void
    {
        if (! $this->activeOrderId) {
            return;
        }
        $order = PharmacyOrder::find($this->activeOrderId);
        if ($order) {
            $this->orderDiscount = (float) $order->discount;
        }
    }

    public function updateQty(int $itemId, int $qty): void
    {
        if (! $this->activeOrderId) {
            return;
        }
        $item = PharmacyOrderItem::where('pharmacy_order_id', $this->activeOrderId)->findOrFail($itemId);
        app(PharmacyService::class)->updateItemQuantity($item, $qty);
        $this->refreshOrderTotals();
    }

    public function incrementQty(int $itemId): void
    {
        $item = PharmacyOrderItem::findOrFail($itemId);
        $this->updateQty($itemId, $item->quantity + 1);
        unset($this->activeOrder);
    }

    public function decrementQty(int $itemId): void
    {
        $item = PharmacyOrderItem::findOrFail($itemId);
        $this->updateQty($itemId, max(1, $item->quantity - 1));
        unset($this->activeOrder);
    }

    public function updateLineDiscount(int $itemId, $value): void
    {
        if (! $this->activeOrderId) {
            return;
        }
        $item = PharmacyOrderItem::where('pharmacy_order_id', $this->activeOrderId)->findOrFail($itemId);
        app(PharmacyService::class)->updateItemDiscount($item, (float) $value);
        $this->refreshOrderTotals();
    }

    public function updatedOrderDiscount($value): void
    {
        if (! $this->activeOrderId) {
            return;
        }
        $order = PharmacyOrder::findOrFail($this->activeOrderId);
        app(PharmacyService::class)->updateOrderDiscount($order, (float) $value);
        unset($this->activeOrder);
    }

    public function updateItemPrice(int $itemId, $price): void
    {
        $item = PharmacyOrderItem::where('pharmacy_order_id', $this->activeOrderId)->findOrFail($itemId);
        app(PharmacyService::class)->updateItemPrice($item, (float) $price);
        unset($this->activeOrder);
    }

    public function updateItemGst(int $itemId, $gst): void
    {
        $item = PharmacyOrderItem::where('pharmacy_order_id', $this->activeOrderId)->findOrFail($itemId);
        app(PharmacyService::class)->updateItemGst($item, (float) $gst);
        unset($this->activeOrder);
    }

    public function removeItem(int $itemId): void
    {
        $item = PharmacyOrderItem::where('pharmacy_order_id', $this->activeOrderId)->findOrFail($itemId);
        app(PharmacyService::class)->removeItem($item);
        unset($this->activeOrder);
    }

    public function openPayment(): void
    {
        if ($this->activeOrder) {
            $this->paidAmount = (float) $this->activeOrder->total;
        }
        $this->showPayment = true;
    }

    public function completeOrder(): void
    {
        if (! $this->activeOrderId) {
            return;
        }
        $order = PharmacyOrder::findOrFail($this->activeOrderId);
        app(PharmacyService::class)->completeOrder(
            $order,
            $this->paidAmount > 0 ? (float) $this->paidAmount : null,
            $this->paymentMethod
        );
        $this->activeOrderId = null;
        $this->showPayment = false;
        $this->tab = 'pending';
        $this->dispatch('notify', title: 'Completed', message: 'Order billed and sent to accounts.', type: 'success');
    }

    #[Computed]
    public function activeOrder(): ?PharmacyOrder
    {
        if (! $this->activeOrderId) {
            return null;
        }

        return PharmacyOrder::with(['items', 'patientVisit.patient', 'prescription', 'patientVisit.consultation'])
            ->find($this->activeOrderId);
    }

    public function getDueAmountProperty(): float
    {
        $total = (float) ($this->activeOrder?->total ?? 0);

        return max(0, round($total - (float) $this->paidAmount, 2));
    }

    public function with(): array
    {
        return [
            'pending' => PatientVisit::with(['patient', 'prescriptions.items'])
                ->where('status', VisitStatus::AtPharmacy)
                ->whereDate('created_at', today())
                ->orderBy('token_number')->get(),
            'activeOrder' => $this->activeOrder,
            'completedToday' => PharmacyOrder::where('status', 'completed')->whereDate('created_at', today())->count(),
            'medicineResults' => Medicine::where('is_active', true)
                ->when($this->medicineSearch, fn ($q) => $q->where(function ($b) {
                    $s = $this->medicineSearch;
                    $b->where('name', 'like', "%{$s}%")
                        ->orWhere('generic_name', 'like', "%{$s}%")
                        ->orWhere('barcode', 'like', "%{$s}%");
                }))
                ->orderBy('name')->limit(12)->get(),
        ];
    }
};
?>

<div wire:poll.12s class="pharmacy-pos">
    <div class="cc-page-header">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-shopping-cart text-primary me-2"></i>Pharmacy POS</h1>
            <p class="cc-page-subtitle"><span class="live-dot"></span> {{ $pending->count() }} pending · {{ $completedToday }} completed today</p>
        </div>
        @if($activeOrder)
            <div class="d-flex gap-2">
                <span class="badge bg-azure-lt px-3 py-2">{{ $activeOrder->order_number }}</span>
            </div>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="premium-card h-100">
                <div class="card-header border-0 pb-0">
                    <ul class="nav nav-tabs cc-tabs card-header-tabs">
                        <li class="nav-item">
                            <button type="button" class="nav-link {{ $tab==='pending'?'active':'' }}" wire:click="$set('tab','pending')">
                                Pending <span class="badge bg-primary ms-1">{{ $pending->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link {{ $tab==='processing'?'active':'' }}" wire:click="$set('tab','processing')">Active</button>
                        </li>
                    </ul>
                </div>
                <div class="list-group list-group-flush pos-queue-list">
                    @if($tab === 'pending')
                        @forelse($pending as $v)
                            <button type="button" wire:click="processVisit({{ $v->id }})" wire:loading.attr="disabled"
                                    class="list-group-item list-group-item-action py-3 border-0 {{ $activeOrder && $activeOrder->patient_visit_id === $v->id ? 'active' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="queue-token" style="font-size:1.6rem">{{ $v->displayToken() }}</span>
                                        @if($v->isEmergency())<span class="badge bg-danger badge-emergency ms-1">ER</span>@endif
                                        <div class="fw-bold mt-1">{{ $v->patient->name }}</div>
                                        <div class="small text-muted">{{ $v->patient->mobile }}</div>
                                    </div>
                                    <i class="ti ti-chevron-right opacity-50"></i>
                                </div>
                            </button>
                        @empty
                            <x-ui.empty-state icon="ti-vaccine-bottle" title="Queue clear" message="No patients at pharmacy" />
                        @endforelse
                    @else
                        @if($activeOrder)
                            <div class="list-group-item active border-0 py-3">
                                <div class="fw-bold">{{ $activeOrder->patientVisit->patient->name }}</div>
                                <div class="small opacity-75">{{ $activeOrder->order_number }}</div>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted small">Select from pending tab</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            @if($activeOrder)
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="premium-card mb-3">
                            <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between border-0">
                                <div>
                                    <h4 class="mb-0">{{ $activeOrder->patientVisit->patient->name }}</h4>
                                    <small class="text-muted">Token {{ $activeOrder->patientVisit->displayToken() }} · {{ $activeOrder->patientVisit->patient->mobile }}</small>
                                </div>
                                <div class="d-flex gap-2 flex-grow-1" style="max-width:420px">
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="ti ti-barcode"></i></span>
                                        <input type="text" wire:model="barcodeInput" wire:keydown.enter="scanBarcode"
                                               class="form-control" placeholder="Scan barcode / SKU" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            @if($activeOrder->patientVisit->consultation?->show_diagnosis_to_pharmacy && $activeOrder->patientVisit->consultation?->diagnosis)
                                <div class="mx-3 mb-2 px-3 py-2 rounded-3 small" style="background:var(--cc-primary-light)">
                                    <i class="ti ti-stethoscope me-1"></i><strong>Diagnosis:</strong> {{ $activeOrder->patientVisit->consultation->diagnosis }}
                                </div>
                            @endif
                            <div class="px-3 pb-3">
                                <div class="position-relative">
                                    <i class="ti ti-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                    <input type="search" wire:model.live.debounce.250ms="medicineSearch" class="form-control ps-5"
                                           placeholder="Search medicine to add (OTC)..." autocomplete="off">
                                </div>
                                @if($medicineSearch && $medicineResults->count())
                                    <div class="list-group mt-2 shadow-sm rounded-3 overflow-hidden" style="max-height:180px;overflow-y:auto">
                                        @foreach($medicineResults as $m)
                                            <button type="button" wire:click="addMedicine({{ $m->id }})"
                                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">
                                                <span><strong>{{ $m->name }}</strong>
                                                    @if($m->generic_name)<small class="text-muted d-block">{{ $m->generic_name }}</small>@endif
                                                </span>
                                                <span class="fw-bold text-primary">₹{{ number_format($m->selling_price, 2) }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover mb-0 pos-medicine-table">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Medicine</th>
                                            <th class="text-center" style="width:140px">Qty</th>
                                            <th>Rate</th>
                                            <th>GST%</th>
                                            <th>Disc.</th>
                                            <th class="text-end">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody wire:key="order-items-{{ $activeOrder->id }}-{{ $activeOrder->items->sum('total') }}">
                                        @foreach($activeOrder->items as $item)
                                            <tr class="pos-medicine-row" wire:key="item-{{ $item->id }}">
                                                <td>
                                                    <div class="fw-semibold"><i class="ti ti-pill text-primary me-1"></i>{{ $item->medicine_name }}</div>
                                                    @if($item->sku)<small class="text-muted">SKU: {{ $item->sku }}</small>@endif
                                                    @if($item->is_otc)<span class="badge bg-secondary-lt">OTC</span>@endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary px-2" wire:click="decrementQty({{ $item->id }})">−</button>
                                                        <input type="number" min="1" class="form-control form-control-sm text-center"
                                                               value="{{ $item->quantity }}"
                                                               wire:change="updateQty({{ $item->id }}, parseInt($event.target.value) || 1)">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary px-2" wire:click="incrementQty({{ $item->id }})">+</button>
                                                    </div>
                                                    <div class="d-flex gap-1 justify-content-center mt-1">
                                                        @foreach([1,2,5,10] as $q)
                                                            <button type="button" class="btn btn-ghost-primary btn-sm py-0 px-1" style="font-size:0.65rem"
                                                                    wire:click="updateQty({{ $item->id }}, {{ $q }})">{{ $q }}</button>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" min="0" step="0.01" class="form-control form-control-sm" style="width:80px"
                                                           value="{{ $item->unit_price }}"
                                                           wire:change="updateItemPrice({{ $item->id }}, $event.target.value)">
                                                </td>
                                                <td>
                                                    <input type="number" min="0" step="0.01" class="form-control form-control-sm" style="width:64px"
                                                           value="{{ $item->gst_percent }}"
                                                           wire:change="updateItemGst({{ $item->id }}, $event.target.value)">
                                                </td>
                                                <td>
                                                    <input type="number" min="0" step="0.01" class="form-control form-control-sm"
                                                           style="width:72px"
                                                           value="{{ $item->discount }}"
                                                           wire:change="updateLineDiscount({{ $item->id }}, $event.target.value)">
                                                </td>
                                                <td class="text-end fw-bold text-primary">₹{{ number_format($item->total, 2) }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-ghost-danger" wire:click="removeItem({{ $item->id }})" title="Remove">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="pos-panel premium-card sticky-top" style="top:calc(var(--cc-topbar-h) + 1rem)">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="ti ti-receipt me-2"></i>Bill Summary</h5>
                                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span wire:key="sub-{{ $activeOrder->subtotal }}">₹{{ number_format($activeOrder->subtotal, 2) }}</span></div>
                                <div class="d-flex justify-content-between mb-2"><span class="text-muted">GST</span><span wire:key="tax-{{ $activeOrder->tax }}">₹{{ number_format($activeOrder->tax, 2) }}</span></div>
                                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                                    <span class="text-muted">Order Discount</span>
                                    <input type="number" min="0" step="0.01" class="form-control form-control-sm" style="width:100px"
                                           wire:model.live.debounce.300ms="orderDiscount">
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-baseline mb-4">
                                    <span class="fw-bold">Grand Total</span>
                                    <span class="pos-total-display" wire:key="total-{{ $activeOrder->total }}">₹{{ number_format($activeOrder->total, 2) }}</span>
                                </div>
                                <a href="{{ route('print.pharmacy-invoice', $activeOrder) }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="ti ti-printer"></i> Print Invoice
                                </a>
                                <button type="button" wire:click="openPayment" class="btn btn-success w-100 btn-lg" wire:loading.attr="disabled">
                                    <i class="ti ti-cash"></i> Complete & Pay
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if($showPayment)
                    <div class="modal show d-block" tabindex="-1" style="background:rgba(15,23,42,0.5)" wire:keydown.escape="$set('showPayment', false)">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius:var(--cc-radius)">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold">Payment</h5>
                                    <button type="button" class="btn-close" wire:click="$set('showPayment', false)"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-4">
                                        <div class="text-muted small">Amount Due</div>
                                        <div class="pos-total-display">₹{{ number_format($activeOrder->total, 2) }}</div>
                                    </div>
                                    <label class="form-label">Payment Method</label>
                                    <div class="pos-pay-grid mb-3">
                                        @foreach(['cash' => 'Cash', 'upi' => 'UPI', 'card' => 'Card'] as $k => $label)
                                            <button type="button" class="pos-pay-btn {{ $paymentMethod === $k ? 'active' : '' }}"
                                                    wire:click="$set('paymentMethod', '{{ $k }}')">{{ $label }}</button>
                                        @endforeach
                                    </div>
                                    <label class="form-label">Paid Amount</label>
                                    <input type="number" min="0" step="0.01" class="form-control form-control-lg mb-2"
                                           wire:model.live="paidAmount">
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Balance Due</span>
                                        <span class="fw-bold {{ $this->dueAmount > 0 ? 'text-warning' : 'text-success' }}">
                                            ₹{{ number_format($this->dueAmount, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-ghost-secondary" wire:click="$set('showPayment', false)">Cancel</button>
                                    <button type="button" class="btn btn-success btn-lg px-4" wire:click="completeOrder" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="completeOrder">Confirm Payment</span>
                                        <span wire:loading wire:target="completeOrder">Processing...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="premium-card cc-empty p-5">
                    <i class="ti ti-scan"></i>
                    <h4 class="fw-bold mt-3">Ready for dispensing</h4>
                    <p class="mb-0">Select a patient from the queue or scan a barcode to begin billing</p>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
