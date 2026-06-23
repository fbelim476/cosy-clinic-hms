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

<div wire:poll.20s class="admin-analytics" x-data="adminCharts()" x-init="init(@js(array_values($chart)), @js(array_keys($chart)))">
    <div class="cc-page-header">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-chart-dots text-primary me-2"></i>Analytics Dashboard</h1>
            <p class="cc-page-subtitle"><span class="live-dot"></span> Enterprise metrics — auto-refreshing</p>
        </div>
    </div>

    <div class="row g-3 mb-4 adm-stats-grid">
        @foreach([
            ['k'=>'today','label'=>"Today's OPD",'icon'=>'ti-users','variant'=>'gradient'],
            ['k'=>'waiting','label'=>'Waiting Queue','icon'=>'ti-hourglass','variant'=>'soft'],
            ['k'=>'revenue','label'=>'Revenue Today','icon'=>'ti-currency-rupee','variant'=>'gradient','prefix'=>'₹'],
            ['k'=>'pharmacy','label'=>'Pharmacy Sales','icon'=>'ti-vaccine','variant'=>'soft','prefix'=>'₹'],
            ['k'=>'emergency','label'=>'Emergency','icon'=>'ti-alert-triangle','variant'=>'soft','danger'=>true],
            ['k'=>'pending_bills','label'=>'Pending Bills','icon'=>'ti-receipt','variant'=>'soft'],
            ['k'=>'low_stock','label'=>'Stock Alerts','icon'=>'ti-package','variant'=>'soft'],
            ['k'=>'doctors_online','label'=>'Doctors Online','icon'=>'ti-stethoscope','variant'=>'soft'],
        ] as $w)
            @php
                $val = $stats[$w['k']] ?? 0;
                $display = ($w['prefix'] ?? '') . (is_float($val) && $val != floor($val) ? number_format($val, 2) : number_format($val, 0));
            @endphp
            <div class="col-6 col-md-4 col-xl-3">
                <div class="adm-stat-card adm-stat-{{ $w['variant'] }} {{ ($w['danger'] ?? false) ? 'adm-stat-danger' : '' }}">
                    <div class="adm-stat-icon-wrap">
                        <i class="ti {{ $w['icon'] }}"></i>
                    </div>
                    <div class="adm-stat-label">{{ $w['label'] }}</div>
                    <div class="adm-stat-value">{{ $display }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 adm-panels-row">
        <div class="col-lg-8">
            <div class="premium-card adm-panel">
                <div class="cc-card-header adm-panel-header">
                    <div class="adm-panel-heading">
                        <span class="adm-panel-icon adm-panel-icon-chart"><i class="ti ti-chart-area-line"></i></span>
                        <div class="adm-panel-titles">
                            <h3 class="adm-panel-title">OPD Visits — Last 7 Days</h3>
                            <p class="adm-panel-subtitle">Daily patient visit trend</p>
                        </div>
                    </div>
                </div>
                <div class="adm-panel-body adm-chart-body">
                    <div id="opdChart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="premium-card adm-panel h-100">
                <div class="cc-card-header adm-panel-header">
                    <div class="adm-panel-heading">
                        <span class="adm-panel-icon adm-panel-icon-live"><i class="ti ti-activity"></i></span>
                        <div class="adm-panel-titles">
                            <h3 class="adm-panel-title">Live Activity</h3>
                            <p class="adm-panel-subtitle">Recent patient movements</p>
                        </div>
                    </div>
                    <span class="badge bg-success-lt adm-live-pill"><span class="live-dot"></span> Live</span>
                </div>
                <ul class="list-group list-group-flush adm-activity-list">
                    @foreach($recent as $v)
                        <li class="list-group-item adm-activity-item">
                            <div class="adm-activity-main">
                                <strong class="adm-activity-name">{{ $v->patient->name }}</strong>
                                <span class="adm-activity-time">{{ $v->created_at->diffForHumans() }}</span>
                            </div>
                            <span class="badge {{ $v->status->badgeClass() }} adm-activity-badge">{{ $v->status->label() }}</span>
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
