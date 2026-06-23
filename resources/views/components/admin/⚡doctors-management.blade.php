<?php

use App\Models\Department;
use App\Models\Doctor;
use App\Services\DoctorService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $filter = 'active';
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public ?int $department_id = null;
    public string $registration_number = '';
    public string $specialization = '';
    public string $qualification = '';
    public string $token_prefix = '';
    public float $consultation_fee = 0;
    public string $room_number = '';
    public ?int $daily_queue_limit = null;
    public bool $is_available = true;
    public $profile_photo;

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
        $doctor = Doctor::with('user')->findOrFail($id);
        $this->editingId = $doctor->id;
        $this->name = $doctor->user->name;
        $this->email = $doctor->user->email;
        $this->phone = $doctor->user->phone ?? '';
        $this->department_id = $doctor->department_id;
        $this->registration_number = $doctor->registration_number ?? '';
        $this->specialization = $doctor->specialization ?? '';
        $this->qualification = $doctor->qualification ?? '';
        $this->token_prefix = $doctor->token_prefix ?? '';
        $this->consultation_fee = (float) $doctor->consultation_fee;
        $this->room_number = $doctor->room_number ?? '';
        $this->daily_queue_limit = $doctor->daily_queue_limit;
        $this->is_available = (bool) $doctor->is_available;
        $this->showForm = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->editingId ? Doctor::find($this->editingId)?->user_id : 'NULL'),
            'department_id' => 'required|exists:departments,id',
            'consultation_fee' => 'required|numeric|min:0',
            'token_prefix' => 'nullable|string|max:10',
        ];

        if (! $this->editingId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'password' => $this->password ?: null,
            'department_id' => $this->department_id,
            'registration_number' => $this->registration_number ?: null,
            'specialization' => $this->specialization ?: null,
            'qualification' => $this->qualification ?: null,
            'token_prefix' => $this->token_prefix ?: null,
            'consultation_fee' => $this->consultation_fee,
            'room_number' => $this->room_number ?: null,
            'daily_queue_limit' => $this->daily_queue_limit,
            'is_available' => $this->is_available,
            'branch_id' => auth()->user()->branch_id ?? 1,
        ];

        if ($this->profile_photo) {
            $data['profile_photo_path'] = $this->profile_photo->store('doctors', 'public');
        }

        $service = app(DoctorService::class);

        if ($this->editingId) {
            $service->update(Doctor::findOrFail($this->editingId), $data);
            $this->dispatch('notify', title: 'Updated', message: 'Doctor profile updated.', type: 'success');
        } else {
            $service->store($data);
            $this->dispatch('notify', title: 'Added', message: 'Doctor account created.', type: 'success');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function toggleStatus(int $id): void
    {
        $doctor = Doctor::findOrFail($id);
        if ($doctor->is_available) {
            app(DoctorService::class)->deactivate($doctor);
            $this->dispatch('notify', title: 'Deactivated', message: 'Doctor deactivated.', type: 'warning');
        } else {
            app(DoctorService::class)->activate($doctor);
            $this->dispatch('notify', title: 'Activated', message: 'Doctor activated.', type: 'success');
        }
    }

    protected function resetForm(): void
    {
        $this->reset([
            'name', 'email', 'phone', 'password', 'department_id',
            'registration_number', 'specialization', 'qualification', 'token_prefix',
            'consultation_fee', 'room_number', 'daily_queue_limit', 'profile_photo',
        ]);
        $this->consultation_fee = 0;
        $this->is_available = true;
    }

    public function with(): array
    {
        $query = Doctor::with(['user', 'department'])
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->filter === 'active', fn ($q) => $q->where('is_available', true))
            ->when($this->filter === 'inactive', fn ($q) => $q->where('is_available', false))
            ->orderByDesc('id');

        return [
            'doctors' => $query->paginate(12),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
        ];
    }
};
?>

<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 fw-bold mb-1">Doctor Management</h2>
            <p class="text-muted mb-0 small">Add doctors, assign departments, configure fees & token prefixes</p>
        </div>
        <button type="button" wire:click="openCreate" class="btn btn-primary">
            <i class="ti ti-user-plus"></i> Add Doctor
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <input wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search doctor name or email...">
        </div>
        <div class="col-md-3">
            <select wire:model.live="filter" class="form-select">
                <option value="all">All Doctors</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="row g-3">
        @forelse($doctors as $doctor)
            <div class="col-md-6 col-xl-4" wire:key="doc-{{ $doctor->id }}">
                <div class="premium-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-bold">{{ $doctor->user->name }}</div>
                            <div class="small text-muted">{{ $doctor->department?->name ?? 'No department' }}</div>
                            <div class="small text-muted">{{ $doctor->user->email }}</div>
                        </div>
                        <span class="badge {{ $doctor->is_available ? 'bg-success' : 'bg-secondary' }}">
                            {{ $doctor->is_available ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3 small">
                        @if($doctor->token_prefix)
                            <span class="badge bg-primary-lt">Prefix: {{ $doctor->token_prefix }}</span>
                        @endif
                        @if($doctor->room_number)
                            <span class="badge bg-azure-lt">Room {{ $doctor->room_number }}</span>
                        @endif
                        <span class="badge bg-secondary-lt">₹{{ number_format($doctor->consultation_fee, 0) }}</span>
                    </div>
                    @if($doctor->specialization)
                        <div class="small mt-2 text-muted">{{ $doctor->specialization }}</div>
                    @endif
                    <div class="d-flex gap-2 mt-3">
                        <button wire:click="openEdit({{ $doctor->id }})" class="btn btn-sm btn-outline-primary flex-fill">Edit</button>
                        <button wire:click="toggleStatus({{ $doctor->id }})" class="btn btn-sm btn-outline-secondary flex-fill">
                            {{ $doctor->is_available ? 'Deactivate' : 'Activate' }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><x-ui.empty-state title="No doctors" message="Add your first doctor to start multi-doctor OPD." /></div>
        @endforelse
    </div>

    <div class="mt-3">{{ $doctors->links() }}</div>

    @if($showForm)
        <div class="modal show d-block cc-med-modal-backdrop" tabindex="-1">
            <div class="modal-dialog modal-lg cc-med-modal-dialog modal-dialog-centered">
                <div class="modal-content cc-med-modal-content border-0 shadow-lg">
                    <div class="modal-header cc-med-modal-header">
                        <h5 class="modal-title fw-bold">{{ $editingId ? 'Edit' : 'Add' }} Doctor</h5>
                        <button type="button" class="btn-close" wire:click="$set('showForm', false)"></button>
                    </div>
                    <form wire:submit="save" class="cc-med-modal-form">
                        <div class="modal-body cc-med-modal-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label class="form-label">Doctor Name *</label><input wire:model="name" class="form-control cc-med-input" required></div>
                                <div class="col-12 col-md-6"><label class="form-label">Email *</label><input type="email" wire:model="email" class="form-control cc-med-input" required></div>
                                <div class="col-12 col-md-4"><label class="form-label">Mobile</label><input wire:model="phone" class="form-control cc-med-input"></div>
                                <div class="col-12 col-md-4"><label class="form-label">Password {{ $editingId ? '' : '*' }}</label><input type="password" wire:model="password" class="form-control cc-med-input" {{ $editingId ? '' : 'required' }}></div>
                                <div class="col-12 col-md-4"><label class="form-label">Department *</label>
                                    <select wire:model="department_id" class="form-select cc-med-input" required>
                                        <option value="">Select</option>
                                        @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4"><label class="form-label">Token Prefix</label><input wire:model="token_prefix" class="form-control cc-med-input" placeholder="DRR, PED, GYN"></div>
                                <div class="col-12 col-md-4"><label class="form-label">Consultation Fee *</label><input type="number" step="0.01" wire:model="consultation_fee" class="form-control cc-med-input"></div>
                                <div class="col-12 col-md-4"><label class="form-label">Room Number</label><input wire:model="room_number" class="form-control cc-med-input" placeholder="101"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Registration No</label><input wire:model="registration_number" class="form-control cc-med-input"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Qualification</label><input wire:model="qualification" class="form-control cc-med-input"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Specialization</label><input wire:model="specialization" class="form-control cc-med-input"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Daily Queue Limit</label><input type="number" wire:model="daily_queue_limit" class="form-control cc-med-input"></div>
                                <div class="col-12"><label class="form-label">Profile Photo</label><input type="file" wire:model="profile_photo" class="form-control cc-med-input" accept="image/*"></div>
                                <div class="col-12"><label class="form-check cc-med-check"><input type="checkbox" wire:model="is_available" class="form-check-input"> Active</label></div>
                            </div>
                        </div>
                        <div class="modal-footer cc-med-modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$set('showForm', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Doctor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));
</script>
@endscript
