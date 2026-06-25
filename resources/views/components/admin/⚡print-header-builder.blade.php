<?php

use App\Models\HospitalBranding;
use Livewire\Component;

new class extends Component
{
    public array $config = [];

    public function mount(): void
    {
        $b = HospitalBranding::forBranch(auth()->user()?->branch_id);
        $this->config = $b?->header_config ?? [
            'enabled' => true, 'logo' => false, 'hospital_name' => true, 'hospital_address' => true,
            'phone' => false, 'email' => false, 'date' => false, 'printed_by' => false, 'qr' => false,
            'align' => 'center', 'logo_size' => 48,
        ];
    }

    public function save(): void
    {
        $b = HospitalBranding::forBranch(auth()->user()?->branch_id);
        HospitalBranding::updateOrCreate(
            ['branch_id' => auth()->user()?->branch_id],
            ['header_config' => $this->config, 'updated_by' => auth()->id()]
        );
        HospitalBranding::clearCache(auth()->user()?->branch_id);
        session()->flash('success', 'Header configuration saved.');
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card cc-glass-card">
        <div class="card-header"><h3 class="card-title">Global Header Builder</h3></div>
        <form wire:submit="save" class="card-body">
            <div class="row g-3">
                @foreach(['enabled' => 'Enable Header', 'logo' => 'Show Logo', 'hospital_name' => 'Hospital Name', 'hospital_address' => 'Address', 'phone' => 'Phone', 'email' => 'Email', 'date' => 'Current Date', 'printed_by' => 'Printed By', 'qr' => 'QR Code'] as $key => $label)
                    <div class="col-md-4">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" wire:model="config.{{ $key }}">
                            <span class="form-check-label">{{ $label }}</span>
                        </label>
                    </div>
                @endforeach
                <div class="col-md-4"><label class="form-label">Alignment</label>
                    <select wire:model="config.align" class="form-select"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select>
                </div>
                <div class="col-md-4"><label class="form-label">Logo Size (px)</label><input type="number" wire:model="config.logo_size" class="form-control"></div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Header</button>
        </form>
    </div>
</div>
