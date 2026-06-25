<?php

namespace App\Services\Print;

use App\Models\HospitalBranding;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Services\PatientService;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintVariableResolver
{
    public function resolve(string $documentType, mixed $subject, ?int $branchId = null): array
    {
        $branding = HospitalBranding::forBranch($branchId);
        $now = now();

        $vars = [
            'hospital_name' => $branding?->hospital_name ?? 'CosyClinic HMS',
            'hospital_address' => $branding?->hospital_address ?? '',
            'hospital_phone' => $branding?->hospital_phone ?? '',
            'hospital_email' => $branding?->hospital_email ?? '',
            'website' => $branding?->website ?? '',
            'gst_number' => $branding?->gst_number ?? '',
            'registration_number' => $branding?->registration_number ?? '',
            'license_number' => $branding?->license_number ?? '',
            'emergency_contact' => $branding?->emergency_contact ?? '',
            'footer_note' => $branding?->footer_note ?? '',
            'terms_conditions' => $branding?->terms_conditions ?? '',
            'tagline' => $branding?->tagline ?? '',
            'logo' => $branding?->logo_path ? asset('storage/'.$branding->logo_path) : '',
            'small_logo' => $branding?->small_logo_path ? asset('storage/'.$branding->small_logo_path) : '',
            'watermark' => $branding?->watermark_path ? asset('storage/'.$branding->watermark_path) : '',
            'primary_color' => $branding?->colors['primary'] ?? '#0ea5e9',
            'secondary_color' => $branding?->colors['secondary'] ?? '#06b6d4',
            'accent_color' => $branding?->colors['accent'] ?? '#3b82f6',
            'date' => $now->format('d/m/Y'),
            'time' => $now->format('h:i A'),
            'datetime' => $now->format('d/m/Y h:i A'),
            'created_by' => auth()->user()?->name ?? 'System',
            'printed_by' => auth()->user()?->name ?? 'System',
            'branch' => auth()->user()?->branch?->name ?? 'Main',
        ];

        return match ($documentType) {
            'opd_token', 'appointment_slip', 'queue_token' => array_merge($vars, $this->fromVisit($subject)),
            'prescription' => array_merge($vars, $this->fromVisit($subject)),
            'patient_card', 'patient_label' => array_merge($vars, $this->fromPatient($subject)),
            'invoice', 'billing', 'receipt' => array_merge($vars, $this->fromInvoice($subject)),
            'pharmacy_bill' => array_merge($vars, $this->fromPharmacyOrder($subject)),
            default => array_merge($vars, $this->fromGeneric($subject)),
        };
    }

    protected function fromVisit(mixed $visit): array
    {
        if (! $visit instanceof PatientVisit) {
            return [];
        }

        $visit->loadMissing(['patient', 'department', 'doctor.user', 'consultation', 'prescriptions.items']);

        $qr = '';
        try {
            $qr = app(PatientService::class)->generateOpdQr($visit);
        } catch (\Throwable) {
            $qr = '';
        }

        $docName = $visit->doctor?->user?->name ?? '';
        if ($docName && ! str_starts_with($docName, 'Dr.')) {
            $docName = 'Dr. '.$docName;
        }

        $prescription = $visit->prescriptions->first();

        return [
            'patient_name' => $visit->patient?->name ?? '',
            'patient_id' => $visit->patient?->patient_id ?? '',
            'patient_age' => (string) ($visit->patient?->age ?? ''),
            'patient_gender' => ucfirst($visit->patient?->gender ?? ''),
            'patient_mobile' => $visit->patient?->mobile ?? '',
            'patient_address' => $visit->patient?->address ?? '',
            'patient_blood_group' => $visit->patient?->blood_group ?? '',
            'patient_city' => $visit->patient?->city ?? '',
            'doctor_name' => $docName,
            'doctor_speciality' => $visit->doctor?->specialization ?? '',
            'department' => $visit->department?->name ?? 'General OPD',
            'visit_no' => $visit->visit_number ?? '',
            'token' => $visit->displayToken(),
            'chief_complaint' => $visit->chief_complaint ?? '',
            'diagnosis' => $visit->consultation?->diagnosis ?? '',
            'instructions' => $prescription?->instructions ?? '',
            'qr' => $qr,
            'barcode' => $visit->patient?->barcode ?? $visit->visit_number ?? '',
        ];
    }

    protected function fromPatient(mixed $patient): array
    {
        if (! $patient instanceof Patient) {
            return [];
        }

        $qr = '';
        try {
            $qr = QrCode::size(80)->generate($patient->patient_id);
        } catch (\Throwable) {
            $qr = '';
        }

        return [
            'patient_name' => $patient->name,
            'patient_id' => $patient->patient_id,
            'patient_age' => (string) ($patient->age ?? ''),
            'patient_gender' => ucfirst($patient->gender ?? ''),
            'patient_mobile' => $patient->mobile ?? '',
            'patient_address' => $patient->address ?? '',
            'patient_blood_group' => $patient->blood_group ?? '',
            'patient_city' => $patient->city ?? '',
            'qr' => $qr,
            'barcode' => $patient->barcode ?? $patient->patient_id,
        ];
    }

    protected function fromInvoice(mixed $invoice): array
    {
        if (! $invoice instanceof Invoice) {
            return [];
        }

        $invoice->loadMissing(['patient', 'items', 'payments']);

        return [
            'patient_name' => $invoice->patient?->name ?? '',
            'patient_id' => $invoice->patient?->patient_id ?? '',
            'patient_mobile' => $invoice->patient?->mobile ?? '',
            'invoice_no' => $invoice->invoice_number ?? '',
            'bill_no' => $invoice->invoice_number ?? '',
            'receipt_no' => $invoice->invoice_number ?? '',
            'payment_mode' => $invoice->payments->last()?->payment_method ?? '',
            'grand_total' => '₹'.number_format((float) $invoice->total, 2),
            'paid_amount' => '₹'.number_format((float) $invoice->paid_amount, 2),
            'due_amount' => '₹'.number_format((float) $invoice->due_amount, 2),
            'items_table' => $this->buildInvoiceTable($invoice),
            'date' => $invoice->created_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];
    }

    protected function fromPharmacyOrder(mixed $order): array
    {
        if (! $order instanceof PharmacyOrder) {
            return [];
        }

        $order->loadMissing(['items', 'patientVisit.patient']);

        return [
            'patient_name' => $order->patientVisit?->patient?->name ?? '',
            'patient_id' => $order->patientVisit?->patient?->patient_id ?? '',
            'invoice_no' => $order->order_number ?? '',
            'bill_no' => $order->order_number ?? '',
            'grand_total' => '₹'.number_format((float) $order->total, 2),
            'paid_amount' => '₹'.number_format((float) $order->paid_amount, 2),
            'due_amount' => '₹'.number_format((float) max(0, $order->total - $order->paid_amount), 2),
            'items_table' => $this->buildPharmacyTable($order),
            'date' => $order->created_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];
    }

    protected function fromGeneric(mixed $subject): array
    {
        return is_array($subject) ? $subject : [];
    }

    protected function buildInvoiceTable(Invoice $invoice): string
    {
        $rows = '';
        foreach ($invoice->items as $item) {
            $rows .= '<tr>'
                .'<td>'.e($item->description).'</td>'
                .'<td style="text-align:center">'.e($item->quantity).'</td>'
                .'<td style="text-align:right">₹'.number_format((float) $item->unit_price, 2).'</td>'
                .'<td style="text-align:right">₹'.number_format((float) $item->total, 2).'</td>'
                .'</tr>';
        }

        return '<table class="pt-table"><thead><tr><th>Description</th><th>Qty</th><th>Rate</th><th>Total</th></tr></thead><tbody>'
            .$rows
            .'<tr><td colspan="3" style="text-align:right;font-weight:bold">Grand Total</td><td style="text-align:right;font-weight:bold">₹'.number_format((float) $invoice->total, 2).'</td></tr>'
            .'<tr><td colspan="3" style="text-align:right">Paid</td><td style="text-align:right">₹'.number_format((float) $invoice->paid_amount, 2).'</td></tr>'
            .'<tr><td colspan="3" style="text-align:right">Due</td><td style="text-align:right">₹'.number_format((float) $invoice->due_amount, 2).'</td></tr>'
            .'</tbody></table>';
    }

    protected function buildPharmacyTable(PharmacyOrder $order): string
    {
        $rows = '';
        foreach ($order->items as $item) {
            $rows .= '<tr>'
                .'<td>'.e($item->medicine_name).'</td>'
                .'<td style="text-align:center">'.e($item->quantity).'</td>'
                .'<td style="text-align:right">₹'.number_format((float) $item->unit_price, 2).'</td>'
                .'<td style="text-align:right">₹'.number_format((float) $item->total, 2).'</td>'
                .'</tr>';
        }

        return '<table class="pt-table"><thead><tr><th>Medicine</th><th>Qty</th><th>Rate</th><th>Total</th></tr></thead><tbody>'
            .$rows
            .'<tr><td colspan="3" style="text-align:right;font-weight:bold">Total</td><td style="text-align:right;font-weight:bold">₹'.number_format((float) $order->total, 2).'</td></tr>'
            .'</tbody></table>';
    }

    public function sampleData(): array
    {
        return [
            'hospital_name' => 'CosyClinic Charitable Trust',
            'hospital_address' => '123 Medical Campus Road, Mumbai - 400001',
            'hospital_phone' => '+91 98765 43210',
            'hospital_email' => 'info@cosyclinic.org',
            'website' => 'www.cosyclinic.org',
            'gst_number' => '27AAAAA0000A1Z5',
            'patient_name' => 'Sample Patient',
            'patient_id' => 'PAT26000001',
            'patient_age' => '35',
            'patient_gender' => 'Male',
            'patient_mobile' => '9876543210',
            'patient_blood_group' => 'B+',
            'patient_city' => 'Mumbai',
            'doctor_name' => 'Dr. Rajesh Kumar',
            'doctor_speciality' => 'General Medicine',
            'department' => 'General OPD',
            'visit_no' => 'VIS2506240001',
            'token' => 'GEN-001',
            'invoice_no' => 'INV25062400001',
            'grand_total' => '₹1,250.00',
            'paid_amount' => '₹1,000.00',
            'due_amount' => '₹250.00',
            'date' => Carbon::now()->format('d/m/Y'),
            'time' => Carbon::now()->format('h:i A'),
            'datetime' => Carbon::now()->format('d/m/Y h:i A'),
            'created_by' => 'Reception Desk',
            'printed_by' => 'Reception Desk',
            'branch' => 'Main Branch',
            'footer_note' => 'Please keep this slip for reference.',
            'terms_conditions' => 'Terms apply.',
            'tagline' => 'Your Health, Our Priority',
            'primary_color' => '#0ea5e9',
            'secondary_color' => '#06b6d4',
            'accent_color' => '#3b82f6',
            'qr' => '<div style="width:80px;height:80px;border:2px dashed #ccc;display:flex;align-items:center;justify-content:center;font-size:10px">QR</div>',
            'barcode' => 'CC00000001',
            'items_table' => '<table class="pt-table"><tr><td>Sample Item</td><td>1</td><td>₹100</td><td>₹100</td></tr></table>',
        ];
    }
}
