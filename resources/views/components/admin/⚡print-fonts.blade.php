<?php

use App\Models\FontLibrary;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $family = '';
    public $fontFile;

    public function save(): void
    {
        $this->validate(['name' => 'required', 'family' => 'required', 'fontFile' => 'required|file|mimes:ttf,otf|max:5120']);
        $path = $this->fontFile->store('fonts', 'public');
        FontLibrary::create(['name' => $this->name, 'family' => $this->family, 'source' => 'upload', 'file_path' => $path, 'is_active' => true]);
        $this->reset(['name', 'family', 'fontFile']);
        session()->flash('success', 'Font uploaded.');
    }

    public function with(): array
    {
        return ['fonts' => FontLibrary::orderBy('name')->get()];
    }
};
?>

<div>
    @include('layouts.partials.print-nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card cc-glass-card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th>Name</th><th>Family</th><th>Source</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($fonts as $f)
                                <tr><td>{{ $f->name }}</td><td>{{ $f->family }}</td><td>{{ ucfirst($f->source) }}</td>
                                    <td><span class="badge {{ $f->is_active ? 'bg-green-lt' : 'bg-secondary' }}">{{ $f->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card cc-glass-card">
                <div class="card-header"><h3 class="card-title">Upload Custom Font</h3></div>
                <form wire:submit="save" class="card-body">
                    <div class="mb-2"><label class="form-label">Display Name</label><input wire:model="name" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">CSS Family</label><input wire:model="family" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">TTF / OTF File</label><input type="file" wire:model="fontFile" class="form-control"></div>
                    <button type="submit" class="btn btn-primary w-100">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
