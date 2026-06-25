<?php

use App\Models\PrintSetting;
use Livewire\Component;

new class extends Component
{
    public string $pdf_engine = 'dompdf';
    public string $default_print_mode = 'browser';
    public bool $auto_print = true;
    public bool $silent_print_ready = false;

    public function mount(): void
    {
        $branchId = auth()->user()?->branch_id;
        $this->pdf_engine = PrintSetting::get('pdf_engine', 'dompdf', $branchId);
        $this->default_print_mode = PrintSetting::get('default_print_mode', 'browser', $branchId);
        $this->auto_print = (bool) PrintSetting::get('auto_print', '1', $branchId);
        $this->silent_print_ready = (bool) PrintSetting::get('silent_print_ready', '0', $branchId);
    }

    public function save(): void
    {
        $branchId = auth()->user()?->branch_id;
        PrintSetting::set('pdf_engine', $this->pdf_engine, 'engine', $branchId);
        PrintSetting::set('default_print_mode', $this->default_print_mode, 'general', $branchId);
        PrintSetting::set('auto_print', $this->auto_print ? '1' : '0', 'general', $branchId);
        PrintSetting::set('silent_print_ready', $this->silent_print_ready ? '1' : '0', 'general', $branchId);
        session()->flash('success', 'Print settings saved.');
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card cc-glass-card" style="max-width:640px">
        <div class="card-header"><h3 class="card-title">Print Engine & Options</h3></div>
        <form wire:submit="save" class="card-body">
            <div class="mb-3">
                <label class="form-label">PDF Engine</label>
                <select wire:model="pdf_engine" class="form-select">
                    <option value="dompdf">DOMPDF</option>
                    <option value="snappy">Snappy (wkhtmltopdf)</option>
                    <option value="browsershot">Browsershot</option>
                    <option value="chrome">Chrome Headless</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Default Print Mode</label>
                <select wire:model="default_print_mode" class="form-select">
                    <option value="browser">Browser Print</option>
                    <option value="pdf">Download PDF</option>
                    <option value="thermal">Thermal Print</option>
                </select>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" wire:model="auto_print" class="form-check-input" id="auto_print">
                <label class="form-check-label" for="auto_print">Auto Print on open</label>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" wire:model="silent_print_ready" class="form-check-input" id="silent">
                <label class="form-check-label" for="silent">Silent Print Ready</label>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
