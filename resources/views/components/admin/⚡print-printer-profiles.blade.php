<?php

use App\Models\PaperSize;
use App\Models\PrinterProfile;
use Livewire\Component;

new class extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $slug = '';
    public string $type = 'pdf';
    public ?int $paper_size_id = null;
    public bool $is_default = false;

    public function openCreate(): void { $this->reset(['editingId','name','slug','type','paper_size_id','is_default']); $this->type='pdf'; $this->showForm=true; }

    public function openEdit(int $id): void
    {
        $p = PrinterProfile::findOrFail($id);
        $this->editingId = $p->id;
        $this->fill($p->only(['name','slug','type','paper_size_id','is_default']));
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate(['name'=>'required','slug'=>'required|unique:printer_profiles,slug,'.$this->editingId,'type'=>'required','paper_size_id'=>'nullable|exists:paper_sizes,id']);
        if ($this->is_default) PrinterProfile::query()->update(['is_default' => false]);
        PrinterProfile::updateOrCreate(['id'=>$this->editingId], [
            'name'=>$this->name,'slug'=>$this->slug,'type'=>$this->type,'paper_size_id'=>$this->paper_size_id,
            'is_default'=>$this->is_default,'is_active'=>true,
            'settings'=>['scale'=>100,'orientation'=>'portrait','copies'=>1,'auto_fit'=>true,'auto_center'=>true],
        ]);
        $this->showForm = false;
        session()->flash('success', 'Printer profile saved.');
    }

    public function with(): array
    {
        return ['profiles'=>PrinterProfile::with('paperSize')->orderBy('name')->get(), 'paperSizes'=>PaperSize::where('is_active',true)->get()];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="d-flex justify-content-end mb-3"><button wire:click="openCreate" class="btn btn-primary"><i class="ti ti-plus"></i> Add Profile</button></div>
    <div class="card cc-glass-card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Name</th><th>Type</th><th>Paper</th><th>Default</th><th></th></tr></thead>
                <tbody>
                    @foreach($profiles as $p)
                        <tr>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td>{{ ucfirst($p->type) }}</td>
                            <td>{{ $p->paperSize?->name ?? '—' }}</td>
                            <td>@if($p->is_default)<span class="badge bg-green-lt">Default</span>@endif</td>
                            <td class="text-end"><button wire:click="openEdit({{ $p->id }})" class="btn btn-sm btn-ghost-primary">Edit</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($showForm)
        <div class="modal modal-blur show d-block" style="background:rgba(0,0,0,.4)">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <form wire:submit="save">
                    <div class="modal-header"><h5 class="modal-title">{{ $editingId?'Edit':'Add' }} Printer Profile</h5><button type="button" class="btn-close" wire:click="$set('showForm',false)"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-12"><label class="form-label">Name</label><input wire:model="name" class="form-control" required></div>
                        <div class="col-12"><label class="form-label">Slug</label><input wire:model="slug" class="form-control" required></div>
                        <div class="col-12"><label class="form-label">Type</label><select wire:model="type" class="form-select"><option value="pdf">PDF</option><option value="laser">Laser</option><option value="thermal">Thermal</option><option value="inkjet">Inkjet</option><option value="network">Network</option></select></div>
                        <div class="col-12"><label class="form-label">Default Paper</label><select wire:model="paper_size_id" class="form-select"><option value="">—</option>@foreach($paperSizes as $ps)<option value="{{ $ps->id }}">{{ $ps->name }}</option>@endforeach</select></div>
                        <div class="col-12"><label class="form-check"><input type="checkbox" wire:model="is_default" class="form-check-input"><span class="form-check-label">Set as default profile</span></label></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn" wire:click="$set('showForm',false)">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
                </form>
            </div></div>
        </div>
    @endif
</div>
