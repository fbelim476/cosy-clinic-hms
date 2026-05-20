<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        protected NumberGeneratorService $numbers,
    ) {}

    public function createInvoice(
        Patient $patient,
        string $type,
        array $items,
        ?PatientVisit $visit = null,
        float $discount = 0,
    ): Invoice {
        return DB::transaction(function () use ($patient, $type, $items, $visit, $discount) {
            $subtotal = collect($items)->sum(fn ($i) => $i['quantity'] * $i['unit_price']);
            $tax = collect($items)->sum(fn ($i) => ($i['tax'] ?? 0));
            $total = $subtotal - $discount + $tax;

            $invoice = Invoice::create([
                'invoice_number' => $this->numbers->invoiceNumber(),
                'patient_id' => $patient->id,
                'patient_visit_id' => $visit?->id,
                'type' => $type,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'due_amount' => $total,
                'payment_status' => 'unpaid',
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax' => $item['tax'] ?? 0,
                    'total' => $item['quantity'] * $item['unit_price'] + ($item['tax'] ?? 0),
                ]);
            }

            return $invoice->load('items');
        });
    }

    public function recordPayment(Invoice $invoice, float $amount, string $method, ?string $ref = null): Payment
    {
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'method' => $method,
            'transaction_ref' => $ref,
            'received_by' => auth()->id(),
            'paid_at' => now(),
        ]);

        $paid = $invoice->paid_amount + $amount;
        $due = max(0, $invoice->total - $paid);

        $invoice->update([
            'paid_amount' => $paid,
            'due_amount' => $due,
            'payment_status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        RealtimeService::paymentReceived(
            auth()->id(),
            $invoice->invoice_number,
            $amount
        );

        return $payment;
    }
}
