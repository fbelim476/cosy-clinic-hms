<?php

use App\Models\HospitalBranding;
use App\Models\PrintTemplate;
use App\Services\Print\PrintTemplateService;
use Livewire\Component;

new class extends Component
{
    public PrintTemplate $template;

    public function mount(PrintTemplate $template): void
    {
        $this->template = $template;
    }

    public function restore(int $versionId): void
    {
        app(PrintTemplateService::class)->restoreVersion($this->template, $versionId);
        session()->flash('success', 'Version restored.');
        $this->redirect(route('admin.print.builder', $this->template), navigate: true);
    }

    public function with(): array
    {
        return ['versions' => $this->template->versions()->with('creator')->get()];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">{{ $template->name }}</h3>
            <span class="text-muted">Version History</span>
        </div>
        <a href="{{ route('admin.print.builder', $template) }}" class="btn btn-primary btn-sm">Back to Builder</a>
    </div>

    <div class="card cc-glass-card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Version</th><th>Note</th><th>By</th><th>Date</th><th></th></tr></thead>
                <tbody>
                    @forelse($versions as $v)
                        <tr>
                            <td><span class="badge bg-azure">v{{ $v->version }}</span></td>
                            <td>{{ $v->note ?? '—' }}</td>
                            <td>{{ $v->creator?->name ?? 'System' }}</td>
                            <td>{{ $v->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($v->version !== $template->version)
                                    <button wire:click="restore({{ $v->id }})" wire:confirm="Restore this version?" class="btn btn-sm btn-ghost-primary">Restore</button>
                                @else
                                    <span class="badge bg-green-lt">Current</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No versions saved yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
