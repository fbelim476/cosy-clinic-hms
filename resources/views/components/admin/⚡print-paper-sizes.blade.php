<?php

use App\Models\PaperSize;
use Livewire\Component;

new class extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $slug = '';
    public string $category = 'standard';
    public float $width_mm = 210;
    public float $height_mm = 297;
    public string $orientation = 'portrait';

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $p = PaperSize::findOrFail($id);
        $this->editingId = $p->id;
        $this->fill($p->only(['name', 'slug', 'category', 'width_mm', 'height_mm', 'orientation']));
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:paper_sizes,slug,'.$this->editingId,
            'width_mm' => 'required|numeric|min:10',
            'height_mm' => 'required|numeric|min:10',
        ]);

        PaperSize::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'width_mm' => $this->width_mm,
            'height_mm' => $this->height_mm,
            'orientation' => $this->orientation,
            'margins' => ['top' => 5, 'right' => 5, 'bottom' => 5, 'left' => 5],
            'is_active' => true,
        ]);

        $this->showForm = false;
        session()->flash('success', 'Paper size saved.');
    }

    public function delete(int $id): void
    {
        $p = PaperSize::findOrFail($id);
        if ($p->is_system) {
            session()->flash('error', 'System paper sizes cannot be deleted.');

            return;
        }
        $p->delete();
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->name = $this->slug = '';
        $this->category = 'standard';
        $this->width_mm = 210;
        $this->height_mm = 297;
        $this->orientation = 'portrait';
    }

    public function with(): array
    {
        return ['sizes' => PaperSize::orderBy('category')->orderBy('name')->get()];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="d-flex justify-content-end mb-3">
        <button wire:click="openCreate" class="btn btn-primary"><i class="ti ti-plus"></i> Add Paper Size</button>
    </div>

    <div class="card cc-glass-card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Name</th><th>Category</th><th>Dimensions</th><th>Orientation</th><th></th></tr></thead>
                <tbody>
                    @foreach($sizes as $s)
                        <tr>
                            <td class="fw-semibold">{{ $s->name }} @if($s->is_system)<span class="badge bg-secondary-lt">System</span>@endif</td>
                            <td>{{ ucfirst($s->category) }}</td>
                            <td>{{ $s->width_mm }} × {{ $s->height_mm }} mm</td>
                            <td>{{ ucfirst($s->orientation) }}</td>
                            <td class="text-end">
                                <button wire:click="openEdit({{ $s->id }})" class="btn btn-sm btn-ghost-primary">Edit</button>
                                @unless($s->is_system)
                                    <button wire:click="delete({{ $s->id }})" wire:confirm="Delete?" class="btn btn-sm btn-ghost-danger">Delete</button>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($showForm)
        <div class="modal modal-blur show d-block" style="background:rgba(0,0,0,.4)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header"><h5 class="modal-title">{{ $editingId ? 'Edit' : 'Add' }} Paper Size</h5>
                            <button type="button" class="btn-close" wire:click="$set('showForm', false)"></button></div>
                        <div class="modal-body row g-3">
                            <div class="col-12"><label class="form-label">Name</label><input wire:model="name" class="form-control" required></div>
                            <div class="col-12"><label class="form-label">Slug</label><input wire:model="slug" class="form-control" required></div>
                            <div class="col-12"><label class="form-label">Category</label>
                                <select wire:model="category" class="form-select">
                                    <option value="standard">Standard</option><option value="thermal">Thermal</option>
                                    <option value="card">Card</option><option value="label">Label</option><option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-6"><label class="form-label">Width (mm)</label><input type="number" step="0.01" wire:model="width_mm" class="form-control"></div>
                            <div class="col-6"><label class="form-label">Height (mm)</label><input type="number" step="0.01" wire:model="height_mm" class="form-control"></div>
                            <div class="col-12"><label class="form-label">Orientation</label>
                                <select wire:model="orientation" class="form-select"><option value="portrait">Portrait</option><option value="landscape">Landscape</option></select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" wire:click="$set('showForm', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
