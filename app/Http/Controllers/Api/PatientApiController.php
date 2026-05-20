<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientApiController extends Controller
{
    public function search(Request $request, PatientService $service)
    {
        return response()->json(
            $service->search($request->get('q', ''), (int) $request->get('limit', 10))
        );
    }

    public function show(Patient $patient)
    {
        $patient->load(['visits.consultation', 'visits.prescriptions.items']);

        return response()->json($patient);
    }
}
