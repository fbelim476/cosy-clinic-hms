<?php

use App\Enums\VisitStatus;
use App\Models\Invoice;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public array $stats = [];

    #[On('dashboard-updated')]
    public function applyStats($payload = []): void
    {
        $stats = is_array($payload) && isset($payload['stats']) ? $payload['stats'] : $payload;
        if (! empty($stats) && is_array($stats)) {
            $this->stats = $stats;
        }
    }

    #[On('queue-updated')]
    public function refreshAll(): void {}

    public function mount(): void
    {
        $this->stats = $this->computeStats();
    }

    public function computeStats(): array
    {
        $todayVisits = PatientVisit::whereDate('created_at', today());

        return [
            'patients' => Patient::count(),
            'today' => (clone $todayVisits)->count(),
            'waiting' => PatientVisit::where('status', VisitStatus::Waiting)->whereDate('created_at', today())->count(),
            'revenue' => (float) Invoice::whereDate('created_at', today())->sum('paid_amount'),
            'pharmacy' => (float) PharmacyOrder::where('status', 'completed')->whereDate('created_at', today())->sum('total'),
            'doctors_online' => User::role('doctor')->where('is_active', true)->count(),
            'low_stock' => Medicine::where('is_active', true)->get()->filter(fn ($m) => $m->stockQuantity() <= $m->reorder_level)->count(),
            'expiring' => MedicineBatch::where('expiry_date', '<=', now()->addDays(30))->where('quantity', '>', 0)->count(),
            'emergency' => PatientVisit::where('priority', 'emergency')->whereDate('created_at', today())->whereNotIn('status', [VisitStatus::Completed])->count(),
            'pending_bills' => Invoice::where('payment_status', '!=', 'paid')->whereDate('created_at', today())->count(),
        ];
    }

    public function chartData(): array
    {
        return PatientVisit::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')->orderBy('date')
            ->pluck('count', 'date')->toArray();
    }

    public function with(): array
    {
        if (empty($this->stats)) {
            $this->stats = $this->computeStats();
        }

        return [
            'stats' => $this->stats,
            'chart' => $this->chartData(),
            'recent' => PatientVisit::with('patient')->latest()->limit(8)->get(),
        ];
    }
};
?>

<div wire:poll.20s x-data="adminCharts()" x-init="init(@js(array_values($chart)), @js(array_keys($chart)))">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold mb-0">Analytics Dashboard</h2>
            <span class="text-muted small"><span class="live-dot"></span> Auto-refreshing metrics</span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['k'=>'today','label'=>"Today's OPD",'icon'=>'ti-users','gradient'=>true],
            ['k'=>'waiting','label'=>'Waiting Queue','icon'=>'ti-hourglass','gradient'=>false],
            ['k'=>'revenue','label'=>'Revenue Today','icon'=>'ti-currency-rupee','gradient'=>true,'prefix'=>'₹'],
            ['k'=>'pharmacy','label'=>'Pharmacy Sales','icon'=>'ti-vaccine','gradient'=>false,'prefix'=>'₹'],
            ['k'=>'emergency','label'=>'Emergency','icon'=>'ti-alert-triangle','gradient'=>false,'danger'=>true],
            ['k'=>'pending_bills','label'=>'Pending Bills','icon'=>'ti-receipt','gradient'=>false],
            ['k'=>'low_stock','label'=>'Stock Alerts','icon'=>'ti-package','gradient'=>false],
            ['k'=>'doctors_online','label'=>'Doctors Online','icon'=>'ti-stethoscope','gradient'=>false],
        ] as $w)
            <div class="col-6 col-md-4 col-xl-3">
                @if($w['gradient'] ?? false)
                    <div class="stat-gradient premium-card p-3 interactive">
                        <i class="ti {{ $w['icon'] }} opacity-50"></i>
                        <div class="small opacity-75 mt-2">{{ $w['label'] }}</div>
                        <div class="stat-value">{{ ($w['prefix'] ?? '') . number_format($stats[$w['k']] ?? 0, ($w['prefix'] ?? false) ? 0 : 0) }}</div>
                    </div>
                @else
                    <div class="stat-soft premium-card p-3 interactive {{ ($w['danger'] ?? false) ? 'border-danger' : '' }}">
                        <i class="ti {{ $w['icon'] }} text-primary"></i>
                        <div class="text-muted small mt-2">{{ $w['label'] }}</div>
                        <div class="fs-4 fw-bold {{ ($w['danger'] ?? false) ? 'text-danger' : '' }}">{{ $stats[$w['k']] ?? 0 }}</div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="premium-card p-3">
                <h4 class="mb-3">OPD Visits — Last 7 Days</h4>
                <div id="opdChart" style="min-height:280px"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="premium-card">
                <div class="card-header border-0"><h4 class="mb-0">Live Activity</h4></div>
                <ul class="list-group list-group-flush">
                    @foreach($recent as $v)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <strong>{{ $v->patient->name }}</strong>
                                <div class="small text-muted">{{ $v->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="badge {{ $v->status->badgeClass() }}">{{ $v->status->label() }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@script
<script>
function adminCharts() {
    return {
        chart: null,
        init(series, labels) {
            if (!window.ApexCharts || !document.querySelector('#opdChart')) return;
            const formatted = labels.map(d => { const p = d.split('-'); return p[2]+'/'+p[1]; });
            this.chart = new ApexCharts(document.querySelector('#opdChart'), {
                chart: { type: 'area', height: 280, toolbar: false, animations: { enabled: true } },
                series: [{ name: 'Visits', data: series }],
                xaxis: { categories: formatted },
                colors: ['#0ea5e9'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1 } },
                stroke: { curve: 'smooth', width: 3 },
                dataLabels: { enabled: false },
            });
            this.chart.render();
            Livewire.on('dashboard-updated', (payload) => {
                if (payload?.stats && this.chart) { /* stats cards refresh via Livewire */ }
            });
        }
    };
}
</script>
@endscript
