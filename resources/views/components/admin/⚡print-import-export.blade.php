<?php

use App\Models\PrintTemplate;
use App\Services\Print\PrintTemplateService;
use Livewire\Component;

new class extends Component
{
    public ?int $export_id = null;
    public string $importJson = '';

    public function export(): void
    {
        if (! $this->export_id) {
            return;
        }
        $data = app(PrintTemplateService::class)->export(PrintTemplate::findOrFail($this->export_id));
        $this->dispatch('download-json', json: json_encode($data, JSON_PRETTY_PRINT), filename: 'template-'.$this->export_id.'.json');
    }

    public function import(): void
    {
        $payload = json_decode($this->importJson, true);
        if (! is_array($payload) || empty($payload['layout'])) {
            session()->flash('error', 'Invalid JSON template file.');

            return;
        }
        $template = app(PrintTemplateService::class)->import($payload);
        session()->flash('success', 'Template imported.');
        $this->redirect(route('admin.print.builder', $template), navigate: true);
    }

    public function with(): array
    {
        return ['templates' => PrintTemplate::orderBy('name')->get()];
    }
};
?>

<div x-data x-on:download-json.window="(() => { const b = new Blob([$event.detail.json], {type:'application/json'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = $event.detail.filename; a.click(); })()">
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card cc-glass-card h-100">
                <div class="card-header"><h3 class="card-title">Export Template</h3></div>
                <div class="card-body">
                    <select wire:model="export_id" class="form-select mb-3">
                        <option value="">Select template...</option>
                        @foreach($templates as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                    </select>
                    <button wire:click="export" class="btn btn-primary" @disabled(!$export_id)><i class="ti ti-download"></i> Export JSON</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card cc-glass-card h-100">
                <div class="card-header"><h3 class="card-title">Import Template</h3></div>
                <div class="card-body">
                    <textarea wire:model="importJson" class="form-control mb-3" rows="8" placeholder='Paste template JSON here...'></textarea>
                    <button wire:click="import" class="btn btn-primary"><i class="ti ti-upload"></i> Import JSON</button>
                </div>
            </div>
        </div>
    </div>
</div>
