<?php

namespace App\Http\Controllers;

use App\Models\HospitalSetting;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Services\PatientService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintController extends Controller
{
    public function opdSlip(PatientVisit $visit)
    {
        $visit->load(['patient', 'department', 'doctor.user']);
        $qr = app(PatientService::class)->generateOpdQr($visit);

        return view('prints.opd-slip', compact('visit', 'qr'));
    }

    public function prescription(PatientVisit $visit)
    {
        $visit->load(['patient', 'consultation', 'prescriptions.items', 'doctor.user']);

        return view('prints.prescription', compact('visit'));
    }

    public function pharmacyInvoice(PharmacyOrder $order)
    {
        $order->load(['items', 'patientVisit.patient']);

        if (request('pdf')) {
            return Pdf::loadView('prints.pharmacy-invoice', compact('order'))->download("invoice-{$order->order_number}.pdf");
        }

        return view('prints.pharmacy-invoice', compact('order'));
    }

    public function invoice(Invoice $invoice)
    {
        $invoice->load(['items', 'patient', 'payments']);

        if (request('pdf')) {
            return Pdf::loadView('prints.invoice', compact('invoice'))->download("invoice-{$invoice->invoice_number}.pdf");
        }

        return view('prints.invoice', compact('invoice'));
    }

    public function patientCard(Patient $patient)
    {
        $qr = QrCode::size(80)->generate($patient->patient_id);

        return view('prints.patient-card', compact('patient', 'qr'));
    }
}
