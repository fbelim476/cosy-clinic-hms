<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request, PatientService $patients)
    {
        $results = $request->q
            ? $patients->search($request->q, 25)
            : Patient::latest()->limit(25)->get();

        return view('pages.reception.patients', compact('results'));
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'visits.consultation',
            'visits.prescriptions.items',
            'reports',
            'invoices.payments',
        ]);

        return view('pages.patient.history', compact('patient'));
    }
}
