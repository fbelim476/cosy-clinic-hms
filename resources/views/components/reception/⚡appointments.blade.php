<?php

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\AppointmentService;
use App\Services\DoctorService;
use Livewire\Component;

new class extends Component
{
    public string $date;
    public ?int $doctor_id = null;
    public ?int $department_id = null;
    public ?int $patient_id = null;
    public string $appointment_time = '10:00';
    public string $notes = '';
    public string $patient_search = '';
    public array $patientResults = [];

    public function mount(): void
    {
        $this->date = today()->toDateString();
    }

    public function updatedPatientSearch(): void
    {
        if (strlen($this->patient_search) < 2) {
            $this->patientResults = [];
            return;
        }

        $this->patientResults = Patient::where('name', 'like', "%{$this->patient_search}%")
            ->orWhere('mobile', 'like', "%{$this->patient_search}%")
            ->limit(8)
            ->get(['id', 'name', 'mobile', 'patient_id'])
            ->toArray();
    }

    public function selectPatient(int $id): void
    {
        $this->patient_id = $id;
        $patient = Patient::find($id);
        $this->patient_search = $patient?->name ?? '';
        $this->patientResults = [];
    }

    public function updatedDepartmentId(): void
    {
        $this->doctor_id = null;
    }

    public function book(): void
    {
        $this->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        try {
            app(AppointmentService::class)->book([
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->doctor_id,
                'department_id' => $this->department_id,
                'appointment_date' => $this->date,
                'appointment_time' => $this->appointment_time,
                'notes' => $this->notes ?: null,
                'branch_id' => auth()->user()->branch_id ?? 1,
            ]);

            $this->dispatch('notify', title: 'Booked', message: 'Appointment scheduled successfully.', type: 'success');
            $this->reset(['patient_id', 'patient_search', 'notes']);
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('notify', title: 'Slot Unavailable', message: $e->getMessage(), type: 'danger');
        }
    }

    public function checkIn(int $appointmentId): void
    {
        $appointment = \App\Models\Appointment::findOrFail($appointmentId);
        $visit = app(AppointmentService::class)->checkIn($appointment, auth()->id());
        $this->dispatch('notify', title: 'Checked In', message: "Token {$visit->displayToken()} generated.", type: 'success');
    }

    public function cancel(int $appointmentId): void
    {
        app(AppointmentService::class)->cancel(\App\Models\Appointment::findOrFail($appointmentId));
        $this->dispatch('notify', title: 'Cancelled', message: 'Appointment cancelled.', type: 'warning');
    }

    public function with(): array
    {
        return [
            'appointments' => app(AppointmentService::class)->listForDate($this->date, $this->doctor_id),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
            'doctors' => app(DoctorService::class)->listActive($this->department_id, auth()->user()->branch_id ?? 1),
            'allDoctors' => Doctor::with('user')->where('is_available', true)->orderBy('id')->get(),
        ];
    }
};
?>

<div>
    <div class="cc-page-header mb-4">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-calendar text-primary me-2"></i>Appointments</h1>
            <p class="cc-page-subtitle">Doctor-wise booking with double-booking prevention</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="premium-card p-4">
                <h4 class="mb-3">Book Appointment</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Search Patient</label>
                        <input wire:model.live.debounce.400ms="patient_search" class="form-control" placeholder="Name or mobile">
                        @if(count($patientResults))
                            <div class="list-group mt-2 rounded overflow-hidden">
                                @foreach($patientResults as $p)
                                    <button type="button" wire:click="selectPatient({{ $p['id'] }})" class="list-group-item list-group-item-action py-2">
                                        {{ $p['name'] }} — {{ $p['mobile'] }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6"><label class="form-label">Date</label><input type="date" wire:model="date" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Time</label><input type="time" wire:model="appointment_time" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Department</label>
                        <select wire:model.live="department_id" class="form-select">
                            <option value="">Select</option>
                            @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label">Doctor</label>
                        <select wire:model="doctor_id" class="form-select" @disabled(! $department_id)>
                            <option value="">Select</option>
                            @foreach($doctors as $doc)<option value="{{ $doc->id }}">{{ $doc->user->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12"><label class="form-label">Notes</label><textarea wire:model="notes" class="form-control" rows="2"></textarea></div>
                    <div class="col-12"><button wire:click="book" class="btn btn-primary w-100">Book Appointment</button></div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="premium-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Schedule — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h4>
                    <select wire:model.live="doctor_id" class="form-select form-select-sm" style="max-width:200px">
                        <option value="">All Doctors</option>
                        @foreach($allDoctors as $doc)<option value="{{ $doc->id }}">{{ $doc->user->name }}</option>@endforeach
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Time</th><th>Patient</th><th>Doctor</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse($appointments as $appt)
                                <tr wire:key="appt-{{ $appt->id }}">
                                    <td>{{ $appt->appointment_time }}</td>
                                    <td>{{ $appt->patient->name }}</td>
                                    <td>{{ $appt->doctor->user->name }}</td>
                                    <td><span class="badge bg-secondary-lt">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span></td>
                                    <td class="text-end">
                                        @if(in_array($appt->status, ['scheduled', 'confirmed']))
                                            <button wire:click="checkIn({{ $appt->id }})" class="btn btn-sm btn-primary">Check In</button>
                                            <button wire:click="cancel({{ $appt->id }})" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center py-4">No appointments for this date.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>$wire.on('notify', (p) => window.showToast?.(p.title, p.message, p.type));</script>
@endscript
