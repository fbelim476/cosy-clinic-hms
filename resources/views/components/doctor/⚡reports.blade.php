<?php

use App\Models\DoctorConsultation;
use App\Models\PatientVisit;
use Livewire\Component;

new class extends Component
{
    public string $range = 'today';

    public function with(): array
    {
        $doctor = auth()->user()->doctor;
        if (! $doctor) {
            return ['stats' => [], 'visits' => collect()];
        }

        $query = PatientVisit::with('patient')->where('doctor_id', $doctor->id);

        if ($this->range === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($this->range === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        $visits = (clone $query)->orderByDesc('created_at')->limit(50)->get();

        $consultations = DoctorConsultation::where('doctor_id', $doctor->id)
            ->when($this->range === 'today', fn ($q) => $q->whereDate('created_at', today()))
            ->when($this->range === 'month', fn ($q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year));

        return [
            'doctor' => $doctor,
            'stats' => [
                'patients' => (clone $query)->count(),
                'completed' => (clone $query)->whereIn('status', ['at_pharmacy', 'completed'])->count(),
                'revenue' => (float) (clone $consultations)->sum('consultation_charge'),
                'consultations' => (clone $consultations)->count(),
            ],
            'visits' => $visits,
        ];
    }
};
?>

<div>
    <div class="cc-page-header mb-4">
        <div>
            <h1 class="cc-page-title"><i class="ti ti-chart-bar text-primary me-2"></i>My Reports</h1>
            <p class="cc-page-subtitle">{{ $doctor?->user->name ?? 'Doctor' }} — performance summary</p>
        </div>
        <select wire:model.live="range" class="form-select" style="max-width:160px">
            <option value="today">Today</option>
            <option value="month">This Month</option>
        </select>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="stat-soft"><div class="stat-label">Patients</div><div class="stat-value">{{ $stats['patients'] ?? 0 }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-soft"><div class="stat-label">Completed</div><div class="stat-value text-success">{{ $stats['completed'] ?? 0 }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-soft"><div class="stat-label">Consultations</div><div class="stat-value">{{ $stats['consultations'] ?? 0 }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-soft"><div class="stat-label">Revenue</div><div class="stat-value">₹{{ number_format($stats['revenue'] ?? 0, 0) }}</div></div></div>
    </div>

    <div class="premium-card p-0">
        <div class="cc-card-header"><h3 class="h5 mb-0">Patient List</h3></div>
        <x-ui.table>
            <x-slot:head><tr><th>Token</th><th>Patient</th><th>Status</th><th>Time</th></tr></x-slot:head>
            @forelse($visits as $v)
                <tr wire:key="rep-{{ $v->id }}">
                    <td class="fw-bold">{{ $v->displayToken() }}</td>
                    <td>{{ $v->patient->name }}</td>
                    <td><span class="badge {{ $v->status->badgeClass() }}">{{ $v->status->label() }}</span></td>
                    <td>{{ $v->created_at->format('h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="4"><x-ui.empty-state title="No data" message="No visits in selected period." /></td></tr>
            @endforelse
        </x-ui.table>
    </div>
</div>
