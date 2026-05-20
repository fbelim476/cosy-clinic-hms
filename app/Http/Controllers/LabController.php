<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function index()
    {
        $pending = LabOrder::with(['patientVisit.patient', 'labTest'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $completed = LabOrder::with(['patientVisit.patient', 'labTest'])
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->latest()
            ->limit(20)
            ->get();

        return view('pages.lab.dashboard', compact('pending', 'completed'));
    }

    public function complete(Request $request, LabOrder $order)
    {
        $data = $request->validate([
            'result_values' => 'nullable|string',
            'notes' => 'nullable|string',
            'report' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
        ]);

        if ($request->hasFile('report')) {
            $data['report_path'] = $request->file('report')->store('lab-reports', 'public');
        }

        $order->update([
            'status' => 'completed',
            'result_values' => $data['result_values'] ?? null,
            'notes' => $data['notes'] ?? null,
            'report_path' => $data['report_path'] ?? $order->report_path,
            'technician_id' => auth()->id(),
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Lab test marked completed.');
    }
}
