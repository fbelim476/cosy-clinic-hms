<?php

use App\Models\HospitalBranding;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $hospital_name = '';
    public string $hospital_address = '';
    public string $hospital_phone = '';
    public string $hospital_email = '';
    public string $website = '';
    public string $gst_number = '';
    public string $registration_number = '';
    public string $license_number = '';
    public string $emergency_contact = '';
    public string $footer_note = '';
    public string $terms_conditions = '';
    public string $tagline = '';
    public string $primary_color = '#0ea5e9';
    public string $secondary_color = '#06b6d4';
    public string $accent_color = '#3b82f6';
    public $logo;
    public $small_logo;
    public $watermark;
    public ?string $logo_path = null;
    public ?string $small_logo_path = null;

    public function mount(): void
    {
        $b = HospitalBranding::forBranch(auth()->user()?->branch_id);
        if ($b) {
            $this->fill($b->only([
                'hospital_name', 'hospital_address', 'hospital_phone', 'hospital_email', 'website',
                'gst_number', 'registration_number', 'license_number', 'emergency_contact',
                'footer_note', 'terms_conditions', 'tagline', 'logo_path', 'small_logo_path',
            ]));
            $this->primary_color = $b->colors['primary'] ?? '#0ea5e9';
            $this->secondary_color = $b->colors['secondary'] ?? '#06b6d4';
            $this->accent_color = $b->colors['accent'] ?? '#3b82f6';
        }
    }

    public function save(): void
    {
        $this->validate([
            'hospital_name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'small_logo' => 'nullable|image|max:1024',
            'watermark' => 'nullable|image|max:2048',
        ]);

        $data = [
            'branch_id' => auth()->user()?->branch_id,
            'hospital_name' => $this->hospital_name,
            'hospital_address' => $this->hospital_address,
            'hospital_phone' => $this->hospital_phone,
            'hospital_email' => $this->hospital_email,
            'website' => $this->website,
            'gst_number' => $this->gst_number,
            'registration_number' => $this->registration_number,
            'license_number' => $this->license_number,
            'emergency_contact' => $this->emergency_contact,
            'footer_note' => $this->footer_note,
            'terms_conditions' => $this->terms_conditions,
            'tagline' => $this->tagline,
            'colors' => ['primary' => $this->primary_color, 'secondary' => $this->secondary_color, 'accent' => $this->accent_color],
            'updated_by' => auth()->id(),
        ];

        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('branding', 'public');
            $this->logo_path = $data['logo_path'];
        }
        if ($this->small_logo) {
            $data['small_logo_path'] = $this->small_logo->store('branding', 'public');
            $this->small_logo_path = $data['small_logo_path'];
        }
        if ($this->watermark) {
            $data['watermark_path'] = $this->watermark->store('branding', 'public');
        }

        HospitalBranding::updateOrCreate(['branch_id' => auth()->user()?->branch_id], $data);
        HospitalBranding::clearCache(auth()->user()?->branch_id);
        session()->flash('success', 'Hospital branding saved.');
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <form wire:submit="save" class="row g-4">
        <div class="col-lg-8">
            <div class="card cc-glass-card mb-3">
                <div class="card-header"><h3 class="card-title">Hospital Identity</h3></div>
                <div class="card-body row g-3">
                    <div class="col-12"><label class="form-label">Hospital Name</label><input wire:model="hospital_name" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Address</label><textarea wire:model="hospital_address" class="form-control" rows="2"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input wire:model="hospital_phone" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input wire:model="hospital_email" type="email" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Website</label><input wire:model="website" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Tagline</label><input wire:model="tagline" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">GST Number</label><input wire:model="gst_number" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Registration No</label><input wire:model="registration_number" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">License No</label><input wire:model="license_number" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Emergency Contact</label><input wire:model="emergency_contact" class="form-control"></div>
                </div>
            </div>
            <div class="card cc-glass-card">
                <div class="card-header"><h3 class="card-title">Footer & Terms</h3></div>
                <div class="card-body row g-3">
                    <div class="col-12"><label class="form-label">Footer Note</label><textarea wire:model="footer_note" class="form-control" rows="2"></textarea></div>
                    <div class="col-12"><label class="form-label">Terms & Conditions</label><textarea wire:model="terms_conditions" class="form-control" rows="3"></textarea></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card cc-glass-card mb-3">
                <div class="card-header"><h3 class="card-title">Logos</h3></div>
                <div class="card-body">
                    @if($logo_path)<img src="{{ asset('storage/'.$logo_path) }}" class="img-fluid mb-2 rounded" alt="Logo">@endif
                    <input type="file" wire:model="logo" class="form-control mb-3" accept="image/*">
                    @if($small_logo_path)<img src="{{ asset('storage/'.$small_logo_path) }}" class="img-fluid mb-2 rounded" style="max-height:60px" alt="Small Logo">@endif
                    <label class="form-label">Small Logo</label>
                    <input type="file" wire:model="small_logo" class="form-control mb-3" accept="image/*">
                    <label class="form-label">Watermark</label>
                    <input type="file" wire:model="watermark" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="card cc-glass-card mb-3">
                <div class="card-header"><h3 class="card-title">Brand Colors</h3></div>
                <div class="card-body">
                    <div class="mb-2"><label class="form-label">Primary</label><input type="color" wire:model.live="primary_color" class="form-control form-control-color w-100"></div>
                    <div class="mb-2"><label class="form-label">Secondary</label><input type="color" wire:model.live="secondary_color" class="form-control form-control-color w-100"></div>
                    <div class="mb-2"><label class="form-label">Accent</label><input type="color" wire:model.live="accent_color" class="form-control form-control-color w-100"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-device-floppy"></i> Save Branding</button>
        </div>
    </form>
</div>
