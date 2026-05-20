<?php

namespace App\Http\Controllers\Api;

use App\Enums\VisitStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;

class QueueApiController extends Controller
{
    public function display()
    {
        return response()->json([
            'current' => PatientVisit::with('patient')
                ->where('status', VisitStatus::WithDoctor)
                ->whereDate('created_at', today())
                ->latest('consultation_started_at')
                ->first(),
            'waiting' => PatientVisit::with('patient')
                ->where('status', VisitStatus::Waiting)
                ->whereDate('created_at', today())
                ->orderBy('token_number')
                ->get(),
            'waiting_count' => PatientVisit::where('status', VisitStatus::Waiting)
                ->whereDate('created_at', today())
                ->count(),
        ]);
    }

    public function stats()
    {
        return response()->json([
            'patients_total' => Patient::count(),
            'visits_today' => PatientVisit::whereDate('created_at', today())->count(),
            'revenue_today' => Invoice::whereDate('created_at', today())->sum('paid_amount'),
            'pharmacy_today' => PharmacyOrder::where('status', 'completed')
                ->whereDate('created_at', today())->sum('total'),
        ]);
    }
}
