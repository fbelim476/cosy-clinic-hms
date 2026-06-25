<?php

use App\Models\HospitalBranding;
use Livewire\Component;

new class extends Component
{
    public array $qr = [];
    public array $barcode = [];

    public function mount(): void
    {
        $b = HospitalBranding::forBranch(auth()->user()?->branch_id);
        $this->qr = $b?->qr_settings ?? ['size' => 80, 'foreground' => '#000000', 'background' => '#ffffff', 'error_correction' => 'M'];
        $this->barcode = $b?->barcode_settings ?? ['type' => 'CODE128', 'width' => 2, 'height' => 40, 'show_text' => true, 'align' => 'center'];
    }

    public function save(): void
    {
        HospitalBranding::updateOrCreate(
            ['branch_id' => auth()->user()?->branch_id],
            ['qr_settings' => $this->qr, 'barcode_settings' => $this->barcode, 'updated_by' => auth()->id()]
        );
        HospitalBranding::clearCache(auth()->user()?->branch_id);
        session()->flash('success', 'QR & Barcode settings saved.');
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <form wire:submit="save" class="row g-4">
        <div class="col-md-6">
            <div class="card cc-glass-card h-100">
                <div class="card-header"><h3 class="card-title">QR Code Settings</h3></div>
                <div class="card-body">
                    <div class="mb-2"><label class="form-label">Size (px)</label><input type="number" wire:model="qr.size" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Foreground</label><input type="color" wire:model="qr.foreground" class="form-control form-control-color w-100"></div>
                    <div class="mb-2"><label class="form-label">Background</label><input type="color" wire:model="qr.background" class="form-control form-control-color w-100"></div>
                    <div class="mb-2"><label class="form-label">Error Correction</label>
                        <select wire:model="qr.error_correction" class="form-select"><option value="L">L</option><option value="M">M</option><option value="Q">Q</option><option value="H">H</option></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card cc-glass-card h-100">
                <div class="card-header"><h3 class="card-title">Barcode Settings</h3></div>
                <div class="card-body">
                    <div class="mb-2"><label class="form-label">Type</label><select wire:model="barcode.type" class="form-select"><option value="CODE128">CODE128</option><option value="CODE39">CODE39</option><option value="EAN13">EAN13</option></select></div>
                    <div class="mb-2"><label class="form-label">Width</label><input type="number" wire:model="barcode.width" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Height</label><input type="number" wire:model="barcode.height" class="form-control"></div>
                    <label class="form-check"><input type="checkbox" wire:model="barcode.show_text" class="form-check-input"><span class="form-check-label">Show Text</span></label>
                </div>
            </div>
        </div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Save Settings</button></div>
    </form>
</div>
