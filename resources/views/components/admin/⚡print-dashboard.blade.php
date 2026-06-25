<?php

use App\Models\PaperSize;
use App\Models\PrintTemplate;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        $templates = PrintTemplate::query();

        return [
            'total' => (clone $templates)->count(),
            'published' => (clone $templates)->where('status', PrintTemplate::STATUS_PUBLISHED)->count(),
            'drafts' => (clone $templates)->where('status', PrintTemplate::STATUS_DRAFT)->count(),
            'thermal' => (clone $templates)->whereHas('paperSize', fn ($q) => $q->where('category', 'thermal'))->count(),
            'a4' => (clone $templates)->whereHas('paperSize', fn ($q) => $q->where('slug', 'a4'))->count(),
            'threeInch' => (clone $templates)->whereHas('paperSize', fn ($q) => $q->where('slug', '3-inch'))->count(),
            'patientCards' => (clone $templates)->where('document_type', 'patient_card')->count(),
            'recent' => PrintTemplate::with('paperSize')->orderByDesc('updated_at')->limit(8)->get(),
        ];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')

    <div class="row g-3 mb-4">
        @foreach([
            ['label' => 'Total Templates', 'value' => $total, 'icon' => 'ti-template', 'color' => '#0ea5e9'],
            ['label' => 'Published', 'value' => $published, 'icon' => 'ti-circle-check', 'color' => '#22c55e'],
            ['label' => 'Drafts', 'value' => $drafts, 'icon' => 'ti-pencil', 'color' => '#f59e0b'],
            ['label' => 'Thermal', 'value' => $thermal, 'icon' => 'ti-receipt', 'color' => '#8b5cf6'],
            ['label' => 'A4', 'value' => $a4, 'icon' => 'ti-file-text', 'color' => '#3b82f6'],
            ['label' => '3 Inch', 'value' => $threeInch, 'icon' => 'ti-ruler', 'color' => '#06b6d4'],
            ['label' => 'Patient Cards', 'value' => $patientCards, 'icon' => 'ti-id', 'color' => '#ec4899'],
        ] as $card)
            <div class="col-6 col-md-4 col-xl">
                <div class="cc-print-stat-card">
                    <div class="cc-print-stat-icon" style="background:{{ $card['color'] }}20;color:{{ $card['color'] }}">
                        <i class="ti {{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="cc-print-stat-value">{{ $card['value'] }}</div>
                        <div class="cc-print-stat-label">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card cc-glass-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Recently Updated Templates</h3>
            <a href="{{ route('admin.print.templates') }}" class="btn btn-primary btn-sm">Manage Templates</a>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Document</th>
                        <th>Paper</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $tpl)
                        <tr>
                            <td class="fw-semibold">{{ $tpl->name }}</td>
                            <td><span class="badge bg-azure-lt">{{ str($tpl->document_type)->headline() }}</span></td>
                            <td>{{ $tpl->paperSize?->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $tpl->isPublished() ? 'bg-green-lt' : 'bg-yellow-lt' }}">
                                    {{ ucfirst($tpl->status) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $tpl->updated_at?->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('admin.print.builder', $tpl) }}" class="btn btn-sm btn-ghost-primary">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No templates yet. Run the Print Management seeder or create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
