<?php

use App\Models\HospitalBranding;
use Livewire\Component;

new class extends Component
{
    public array $config = [];

    public function mount(): void
    {
        $b = HospitalBranding::forBranch(auth()->user()?->branch_id);
        $this->config = $b?->footer_config ?? [
            'enabled' => true, 'thank_you' => true, 'thank_you_text' => 'Thank you for visiting us.',
            'footer_text' => true, 'terms' => false, 'page_number' => false, 'align' => 'center',
        ];
    }

    public function save(): void
    {
        HospitalBranding::updateOrCreate(
            ['branch_id' => auth()->user()?->branch_id],
            ['footer_config' => $this->config, 'updated_by' => auth()->id()]
        );
        HospitalBranding::clearCache(auth()->user()?->branch_id);
        session()->flash('success', 'Footer configuration saved.');
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card cc-glass-card">
        <div class="card-header"><h3 class="card-title">Global Footer Builder</h3></div>
        <form wire:submit="save" class="card-body">
            <div class="row g-3">
                @foreach(['enabled' => 'Enable Footer', 'thank_you' => 'Thank You Message', 'footer_text' => 'Footer Note', 'terms' => 'Terms & Conditions', 'page_number' => 'Page Number'] as $key => $label)
                    <div class="col-md-4"><label class="form-check"><input type="checkbox" class="form-check-input" wire:model="config.{{ $key }}"><span class="form-check-label">{{ $label }}</span></label></div>
                @endforeach
                <div class="col-12"><label class="form-label">Thank You Text</label><input wire:model="config.thank_you_text" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Alignment</label><select wire:model="config.align" class="form-select"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Footer</button>
        </form>
    </div>
</div>
