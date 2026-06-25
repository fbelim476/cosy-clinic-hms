<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Services\Print\PrintEngineService;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function __construct(protected PrintEngineService $engine) {}

    public function opdSlip(PatientVisit $visit)
    {
        return $this->render('opd_token', $visit, 'OPD Slip '.$visit->displayToken());
    }

    public function prescription(PatientVisit $visit)
    {
        return $this->render('prescription', $visit, 'Prescription');
    }

    public function pharmacyInvoice(PharmacyOrder $order)
    {
        return $this->render('pharmacy_bill', $order, 'Pharmacy Bill '.$order->order_number);
    }

    public function invoice(Invoice $invoice)
    {
        return $this->render('invoice', $invoice, 'Invoice '.$invoice->invoice_number);
    }

    public function patientCard(Patient $patient)
    {
        return $this->render('patient_card', $patient, 'Patient Card '.$patient->patient_id);
    }

    protected function render(string $documentType, mixed $subject, string $title)
    {
        $result = $this->engine->render($documentType, $subject);
        $embed = request()->boolean('embed');
        $autoPrint = $result['template']?->settings['auto_print'] ?? true;

        if (request('pdf') && $result['paper']) {
            return $this->engine->downloadPdf(
                $result['html'],
                $result['paper'],
                str($title)->slug().'.pdf'
            );
        }

        return view('prints.dynamic', [
            'html' => $result['html'],
            'title' => $title,
            'embed' => $embed,
            'autoPrint' => $autoPrint && ! $embed,
            'paperCss' => null,
            'pdfUrl' => request()->fullUrlWithQuery(['pdf' => 1]),
        ]);
    }
}
