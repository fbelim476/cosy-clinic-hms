<?php

use App\Models\PaperSize;
use App\Models\PrintTemplate;
use App\Services\Print\PrintEngineService;
use Livewire\Component;

new class extends Component
{
    public ?int $template_id = null;
    public string $previewMode = 'desktop';
    public ?string $previewHtml = null;

    public function updatedTemplateId(): void
    {
        $this->renderPreview();
    }

    public function renderPreview(): void
    {
        if (! $this->template_id) {
            $this->previewHtml = null;

            return;
        }
        $template = PrintTemplate::with('paperSize')->find($this->template_id);
        $this->previewHtml = $template ? app(PrintEngineService::class)->preview($template) : null;
    }

    public function with(): array
    {
        return [
            'templates' => PrintTemplate::orderBy('name')->get(),
            'paperSizes' => PaperSize::orderBy('name')->get(),
        ];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    <div class="card cc-glass-card mb-3">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">
            <select wire:model.live="template_id" class="form-select" style="width:auto;min-width:240px">
                <option value="">Select template...</option>
                @foreach($templates as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
            <div class="btn-group">
                @foreach(['desktop', 'tablet', 'mobile', 'thermal', 'a4'] as $mode)
                    <button type="button" wire:click="$set('previewMode', '{{ $mode }}')" class="btn btn-sm {{ $previewMode === $mode ? 'btn-primary' : 'btn-ghost-secondary' }}">{{ ucfirst($mode) }}</button>
                @endforeach
            </div>
            <button wire:click="renderPreview" class="btn btn-sm btn-primary"><i class="ti ti-refresh"></i> Refresh</button>
        </div>
    </div>

    @if($previewHtml)
        <div class="cc-print-preview-frame" data-mode="{{ $previewMode }}">
            <div class="cc-print-preview-inner">{!! $previewHtml !!}</div>
        </div>
    @else
        <div class="alert alert-info">Select a template to preview with sample data.</div>
    @endif
</div>
