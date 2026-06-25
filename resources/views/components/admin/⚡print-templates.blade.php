<?php

use App\Models\PaperSize;
use App\Models\PrintTemplate;
use App\Services\Print\PrintTemplateService;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterType = '';

    public bool $showCreate = false;
    public string $name = '';
    public string $document_type = 'opd_token';
    public ?int $paper_size_id = null;

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['name', 'document_type', 'paper_size_id']);
        $this->document_type = 'opd_token';
        $this->showCreate = true;
    }

    public function create(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'required|string|max:64',
            'paper_size_id' => 'nullable|exists:paper_sizes,id',
        ]);

        $template = app(PrintTemplateService::class)->store([
            'name' => $this->name,
            'document_type' => $this->document_type,
            'paper_size_id' => $this->paper_size_id,
        ]);

        $this->showCreate = false;
        $this->redirect(route('admin.print.builder', $template), navigate: true);
    }

    public function publish(int $id): void
    {
        $template = PrintTemplate::findOrFail($id);
        app(PrintTemplateService::class)->publish($template);
        session()->flash('success', 'Template published.');
    }

    public function duplicate(int $id): void
    {
        $copy = app(PrintTemplateService::class)->duplicate(PrintTemplate::findOrFail($id));
        session()->flash('success', 'Template duplicated.');
        $this->redirect(route('admin.print.builder', $copy), navigate: true);
    }

    public function delete(int $id): void
    {
        PrintTemplate::findOrFail($id)->delete();
        session()->flash('success', 'Template deleted.');
    }

    public function with(): array
    {
        $query = PrintTemplate::with('paperSize')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType, fn ($q) => $q->where('document_type', $this->filterType))
            ->orderByDesc('updated_at');

        return [
            'templates' => $query->paginate(12),
            'paperSizes' => PaperSize::where('is_active', true)->orderBy('name')->get(),
            'documentTypes' => $this->documentTypes(),
        ];
    }

    protected function documentTypes(): array
    {
        return [
            'opd_token' => 'OPD Token', 'patient_card' => 'Patient Card', 'invoice' => 'Invoice',
            'receipt' => 'Receipt', 'billing' => 'Billing', 'lab_report' => 'Lab Report',
            'prescription' => 'Prescription', 'appointment_slip' => 'Appointment Slip',
            'pharmacy_bill' => 'Pharmacy Bill', 'patient_label' => 'Patient Label',
            'discharge_summary' => 'Discharge Summary', 'admission_slip' => 'Admission Slip',
            'referral_letter' => 'Referral Letter', 'birth_certificate' => 'Birth Certificate',
            'death_certificate' => 'Death Certificate', 'medical_certificate' => 'Medical Certificate',
            'doctor_certificate' => 'Doctor Certificate', 'custom' => 'Custom Template',
        ];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <div class="d-flex flex-wrap gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search templates..." style="min-width:200px">
            <select wire:model.live="filterStatus" class="form-select" style="width:auto">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
            <select wire:model.live="filterType" class="form-select" style="width:auto">
                <option value="">All Documents</option>
                @foreach($documentTypes as $k => $v)
                    <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" wire:click="openCreate" class="btn btn-primary"><i class="ti ti-plus"></i> New Template</button>
    </div>

    <div class="row g-3">
        @forelse($templates as $tpl)
            <div class="col-md-6 col-xl-4" wire:key="tpl-{{ $tpl->id }}">
                <div class="card cc-glass-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h4 class="mb-1">{{ $tpl->name }}</h4>
                                <span class="badge bg-azure-lt">{{ $documentTypes[$tpl->document_type] ?? $tpl->document_type }}</span>
                            </div>
                            <span class="badge {{ $tpl->isPublished() ? 'bg-green' : 'bg-yellow' }}">{{ ucfirst($tpl->status) }}</span>
                        </div>
                        <div class="text-muted small mb-3">
                            <i class="ti ti-dimensions"></i> {{ $tpl->paperSize?->name ?? 'Default' }}
                            @if($tpl->is_default) · <span class="text-primary">Default</span> @endif
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ route('admin.print.builder', $tpl) }}" class="btn btn-sm btn-primary">Design</a>
                            @if(!$tpl->isPublished())
                                <button wire:click="publish({{ $tpl->id }})" class="btn btn-sm btn-success">Publish</button>
                            @endif
                            <button wire:click="duplicate({{ $tpl->id }})" class="btn btn-sm btn-ghost-secondary">Duplicate</button>
                            <a href="{{ route('admin.print.versions', $tpl) }}" class="btn btn-sm btn-ghost-secondary">Versions</a>
                            <button wire:click="delete({{ $tpl->id }})" wire:confirm="Delete this template?" class="btn btn-sm btn-ghost-danger">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">No templates found.</div></div>
        @endforelse
    </div>

    <div class="mt-3">{{ $templates->links() }}</div>

    @if($showCreate)
        <div class="modal modal-blur show d-block" tabindex="-1" style="background:rgba(0,0,0,.4)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">New Print Template</h5>
                        <button type="button" class="btn-close" wire:click="$set('showCreate', false)"></button>
                    </div>
                    <form wire:submit="create">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Template Name</label>
                                <input type="text" wire:model="name" class="form-control" required>
                                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Document Type</label>
                                <select wire:model="document_type" class="form-select">
                                    @foreach($documentTypes as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Paper Size</label>
                                <select wire:model="paper_size_id" class="form-select">
                                    <option value="">Default</option>
                                    @foreach($paperSizes as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" wire:click="$set('showCreate', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create & Design</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
