<?php

namespace App\Http\Controllers\Api;

use App\Enums\VisitStatus;
use App\Http\Controllers\Controller;
use App\Models\PatientVisit;

class VisitApiController extends Controller
{
    public function queue()
    {
        $visits = PatientVisit::with(['patient', 'doctor.user'])
            ->whereIn('status', [VisitStatus::Waiting, VisitStatus::WithDoctor, VisitStatus::Registered])
            ->whereDate('created_at', today())
            ->orderBy('token_number')
            ->get();

        return response()->json($visits);
    }

    public function show(PatientVisit $visit)
    {
        $visit->load(['patient', 'consultation', 'prescriptions.items', 'pharmacyOrders.items']);

        return response()->json($visit);
    }
}
