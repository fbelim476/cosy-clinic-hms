<?php

use App\Exports\MedicinesExport;
use App\Exports\MedicinesTemplateExport;
use App\Imports\MedicinesImport;
use App\Models\Medicine;
use App\Services\MedicineService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $filter = 'all';
    public bool $showForm = false;
    public bool $showImport = false;
    public ?int $editingId = null;
    public $importFile;
    public ?array $importReport = null;

    public string $name = '';
    public string $generic_name = '';
    public string $sku = '';
    public string $barcode = '';
    public string $category = '';
    public string $medicine_type = 'tablet';
    public string $manufacturer = '';
    public string $strength = '';
    public string $unit = 'strip';
    public float $selling_price = 0;
    public float $purchase_price = 0;
    public float $gst_percent = 5;
    public int $reorder_level = 10;
    public int $initial_stock = 0;
    public string $batch_number = '';
    public ?string $expiry_date = null;
    public string $description = '';
    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $m = Medicine::findOrFail($id);
        $this->editingId = $m->id;
        $this->name = $m->name;
        $this->generic_name = $m->generic_name ?? '';
        $this->sku = $m->sku ?? '';
        $this->barcode = $m->barcode ?? '';
        $this->category = $m->category ?? '';
        $this->medicine_type = $m->medicine_type ?? 'tablet';
        $this->manufacturer = $m->manufacturer ?? '';
        $this->strength = $m->strength ?? '';
        $this->unit = $m->unit ?? 'strip';
        $this->selling_price = (float) $m->selling_price;
        $this->purchase_price = (float) ($m->purchase_price ?? 0);
        $this->gst_percent = (float) $m->gst_percent;
        $this->reorder_level = (int) $m->reorder_level;
        $this->description = $m->description ?? '';
        $this->is_active = (bool) $m->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|unique:medicines,sku,' . ($this->editingId ?? 'NULL'),
        ]);

        $data = [
            'name' => $this->name,
            'generic_name' => $this->generic_name ?: null,
            'sku' => $this->sku ?: null,
            'barcode' => $this->barcode ?: null,
            'category' => $this->category ?: null,
            'medicine_type' => $this->medicine_type,
            'manufacturer' => $this->manufacturer ?: null,
            'strength' => $this->strength ?: null,
            'unit' => $this->unit,
            'selling_price' => $this->selling_price,
            'purchase_price' => $this->purchase_price,
            'gst_percent' => $this->gst_percent,
            'reorder_level' => $this->reorder_level,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            app(MedicineService::class)->update(Medicine::findOrFail($this->editingId), $data);
            $this->dispatch('notify', title: 'Updated', message: 'Medicine updated successfully.', type: 'success');
        } else {
            app(MedicineService::class)->store(array_merge($data, [
                'batch_number' => $this->batch_number ?: 'INIT',
                'expiry_date' => $this->expiry_date,
            ]), $this->initial_stock > 0 ? $this->initial_stock : null);
            $this->dispatch('notify', title: 'Added', message: 'Medicine added to inventory.', type: 'success');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        app(MedicineService::class)->softDelete(Medicine::findOrFail($id));
        $this->dispatch('notify', title: 'Deleted', message: 'Medicine moved to trash.', type: 'warning');
    }

    public function restore(int $id): void
    {
        app(MedicineService::class)->restore($id);
        $this->dispatch('notify', title: 'Restored', message: 'Medicine restored.', type: 'success');
    }

    public function runImport(): void
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        $import = new MedicinesImport;
        Excel::import($import, $this->importFile->getRealPath());
        $this->importReport = [
            'imported' => count($import->imported),
            'skipped' => count($import->skipped),
            'errors' => count($import->errors),
            'skipped_list' => array_slice($import->skipped, 0, 10),
            'error_list' => array_slice($import->errors, 0, 10),
        ];
        $this->importFile = null;
        $this->dispatch('notify', title: 'Import Complete', message: "{$this->importReport['imported']} medicines imported.", type: 'success');
    }

    public function export()
    {
        return Excel::download(new MedicinesExport, 'medicines-' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new MedicinesTemplateExport, 'medicines-import-template.xlsx');
    }

    protected function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'generic_name', 'sku', 'barcode', 'category', 'medicine_type',
            'manufacturer', 'strength', 'unit', 'selling_price', 'purchase_price', 'gst_percent',
            'reorder_level', 'initial_stock', 'batch_number', 'expiry_date', 'description',
        ]);
        $this->medicine_type = 'tablet';
        $this->unit = 'strip';
        $this->gst_percent = 5;
        $this->reorder_level = 10;
        $this->is_active = true;
    }

    public function with(): array
    {
        $q = Medicine::query()->with('batches');

        if ($this->filter === 'low_stock') {
            $q->where('is_active', true)
                ->whereRaw('(SELECT COALESCE(SUM(quantity),0) FROM medicine_batches WHERE medicine_id = medicines.id) <= medicines.reorder_level');
        } elseif ($this->filter === 'inactive') {
            $q->where('is_active', false);
        } elseif ($this->filter === 'trashed') {
            $q->onlyTrashed();
        } else {
            $q->where('is_active', true);
        }

        if ($this->search) {
            $s = $this->search;
            $q->where(fn ($b) => $b->where('name', 'like', "%{$s}%")
                ->orWhere('generic_name', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%")
                ->orWhere('barcode', 'like', "%{$s}%"));
        }

        $all = Medicine::where('is_active', true)->get();
        $lowStock = $all->filter(fn ($m) => $m->stockQuantity() <= $m->reorder_level)->count();

        return [
            'medicines' => $q->orderBy('name')->paginate(12),
            'stats' => [
                'total' => Medicine::where('is_active', true)->count(),
                'low_stock' => $lowStock,
                'trashed' => Medicine::onlyTrashed()->count(),
            ],
        ];
    }
};
?>

<div>
    <div class="cc-page-header">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-pill text-primary me-2"></i>Medicine Inventory</h1>
            <p class="cc-page-subtitle">Professional pharmacy stock management</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" wire:click="downloadTemplate" class="btn btn-outline-secondary btn-sm"><i class="ti ti-download"></i> Template</button>
            <button type="button" wire:click="export" class="btn btn-outline-primary btn-sm"><i class="ti ti-file-export"></i> Export</button>
            <button type="button" wire:click="$set('showImport', true)" class="btn btn-outline-azure btn-sm"><i class="ti ti-upload"></i> Import Excel</button>
            <button type="button" wire:click="openCreate" class="btn btn-primary"><i class="ti ti-plus"></i> Add Medicine</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="cc-stat-card"><div class="cc-stat-label">Active Medicines</div><div class="cc-stat-value text-primary">{{ $stats['total'] }}</div></div></div>
        <div class="col-md-4"><div class="cc-stat-card"><div class="cc-stat-label">Low Stock</div><div class="cc-stat-value text-warning">{{ $stats['low_stock'] }}</div></div></div>
        <div class="col-md-4"><div class="cc-stat-card"><div class="cc-stat-label">In Trash</div><div class="cc-stat-value text-danger">{{ $stats['trashed'] }}</div></div></div>
    </div>

    <div class="premium-card p-3 mb-3 sticky-top" style="top:calc(var(--cc-topbar-h) + 0.5rem);z-index:10">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search name, SKU, barcode...">
                </div>
            </div>
            <div class="col-md-6">
                <select wire:model.live="filter" class="form-select">
                    <option value="all">All Active</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="inactive">Inactive</option>
                    <option value="trashed">Deleted / Trash</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($medicines as $m)
            @php $stock = $m->stockQuantity(); $low = $stock <= $m->reorder_level; @endphp
            <div class="col-md-6 col-xl-4" wire:key="med-{{ $m->id }}">
                <div class="premium-card h-100 p-3 {{ $low ? 'border-warning' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $m->name }}</h5>
                            <small class="text-muted">{{ $m->generic_name }}</small>
                        </div>
                        <span class="badge bg-{{ $m->is_active ? 'success' : 'secondary' }}-lt">{{ $m->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 small mb-3">
                        <span class="badge bg-secondary-lt"><code>{{ $m->sku ?? '—' }}</code></span>
                        <span class="badge bg-primary-lt">₹{{ number_format($m->selling_price, 2) }}</span>
                        <span class="badge bg-{{ $low ? 'danger' : 'success' }}-lt">Stock: {{ $stock }}</span>
                        <span class="badge bg-azure-lt">GST {{ $m->gst_percent }}%</span>
                    </div>
                    <div class="d-flex gap-1">
                        @if($m->trashed())
                            <button wire:click="restore({{ $m->id }})" class="btn btn-sm btn-success flex-fill">Restore</button>
                        @else
                            <button wire:click="openEdit({{ $m->id }})" class="btn btn-sm btn-outline-primary flex-fill"><i class="ti ti-edit"></i> Edit</button>
                            <button wire:click="delete({{ $m->id }})" wire:confirm="Delete this medicine?" class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><x-ui.empty-state icon="ti-pill" title="No medicines" message="Add or import medicines to get started" /></div>
        @endforelse
    </div>
    <div class="mt-3">{{ $medicines->links() }}</div>

    @if($showForm)
        <div class="modal show d-block cc-med-modal-backdrop" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-lg cc-med-modal-dialog modal-dialog-centered">
                <div class="modal-content cc-med-modal-content border-0 shadow-lg">
                    <div class="modal-header cc-med-modal-header">
                        <h5 class="modal-title fw-bold">{{ $editingId ? 'Edit' : 'Add' }} Medicine</h5>
                        <button type="button" class="btn-close" wire:click="$set('showForm', false)" aria-label="Close"></button>
                    </div>
                    <form wire:submit="save" class="cc-med-modal-form">
                        <div class="modal-body cc-med-modal-body">
                            <div class="row g-3 cc-med-form-grid">
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label class="form-label cc-med-label">Name *</label>
                                    <input wire:model="name" class="form-control cc-med-input" required>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label class="form-label cc-med-label">Generic Name</label>
                                    <input wire:model="generic_name" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">SKU</label>
                                    <input wire:model="sku" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">Barcode</label>
                                    <input wire:model="barcode" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">Type</label>
                                    <select wire:model="medicine_type" class="form-select cc-med-input">
                                        <option value="tablet">Tablet</option>
                                        <option value="syrup">Syrup</option>
                                        <option value="injection">Injection</option>
                                        <option value="capsule">Capsule</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">Selling Price *</label>
                                    <input type="number" step="0.01" wire:model="selling_price" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">Purchase Price</label>
                                    <input type="number" step="0.01" wire:model="purchase_price" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">GST %</label>
                                    <input type="number" step="0.01" wire:model="gst_percent" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">Strength</label>
                                    <input wire:model="strength" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">Manufacturer</label>
                                    <input wire:model="manufacturer" class="form-control cc-med-input">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label cc-med-label">Reorder Level</label>
                                    <input type="number" wire:model="reorder_level" class="form-control cc-med-input">
                                </div>
                                @unless($editingId)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label cc-med-label">Initial Stock</label>
                                        <input type="number" wire:model="initial_stock" class="form-control cc-med-input">
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label cc-med-label">Batch No</label>
                                        <input wire:model="batch_number" class="form-control cc-med-input">
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label cc-med-label">Expiry</label>
                                        <input type="date" wire:model="expiry_date" class="form-control cc-med-input">
                                    </div>
                                @endunless
                                <div class="col-12">
                                    <label class="form-label cc-med-label">Description</label>
                                    <textarea wire:model="description" class="form-control cc-med-textarea" rows="3"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-check cc-med-check">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input">
                                        <span class="form-check-label">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer cc-med-modal-footer">
                            <button type="button" class="btn btn-secondary cc-med-btn-cancel" wire:click="$set('showForm', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary cc-med-btn-save">Save Medicine</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showImport)
        <div class="modal show d-block" style="background:rgba(15,23,42,0.5)">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg" style="border-radius:16px">
                    <div class="modal-header"><h5 class="modal-title fw-bold">Import Medicines (Excel/CSV)</h5>
                        <button type="button" class="btn-close" wire:click="$set('showImport', false)"></button></div>
                    <div class="modal-body">
                        <p class="small text-muted">Download the template first. Duplicate SKUs will be skipped.</p>
                        <input type="file" wire:model="importFile" class="form-control" accept=".xlsx,.xls,.csv">
                        @if($importReport)
                            <div class="alert alert-info mt-3 small mb-0">
                                Imported: <strong>{{ $importReport['imported'] }}</strong> ·
                                Skipped: {{ $importReport['skipped'] }} · Errors: {{ $importReport['errors'] }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button wire:click="downloadTemplate" type="button" class="btn btn-outline-secondary">Download Template</button>
                        <button wire:click="runImport" class="btn btn-primary" wire:loading.attr="disabled">Upload & Import</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
