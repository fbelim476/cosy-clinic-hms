<?php

namespace App\Services;

use App\Enums\VisitStatus;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Models\PharmacyOrderItem;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class PharmacyService
{
    public function createOrderFromVisit(PatientVisit $visit, int $pharmacistId): PharmacyOrder
    {
        return DB::transaction(function () use ($visit, $pharmacistId) {
            $prescription = $visit->prescriptions()
                ->where('visibility', 'public')
                ->where('status', 'active')
                ->latest()
                ->first();

            $order = PharmacyOrder::create([
                'order_number' => 'PH' . date('ymdHis'),
                'patient_visit_id' => $visit->id,
                'prescription_id' => $prescription?->id,
                'pharmacist_id' => $pharmacistId,
                'status' => 'pending',
            ]);

            if ($prescription) {
                foreach ($prescription->items as $item) {
                    $medicine = $item->medicine;
                    $price = $medicine?->selling_price ?? 0;
                    $qty = $item->quantity;
                    $total = $price * $qty;

                    PharmacyOrderItem::create([
                        'pharmacy_order_id' => $order->id,
                        'medicine_id' => $item->medicine_id,
                        'prescription_item_id' => $item->id,
                        'medicine_name' => $item->medicine_name,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'gst_percent' => $medicine?->gst_percent ?? 0,
                        'total' => $total,
                    ]);
                }
            }

            $this->recalculateOrder($order);

            $order = $order->load(['items', 'patientVisit.patient']);
            RealtimeService::queueUpdated(
                'pharmacy_order_created',
                $order->patientVisit,
                "Pharmacy processing {$order->patientVisit->patient->name}"
            );

            return $order;
        });
    }

    public function recalculateOrder(PharmacyOrder $order): void
    {
        $subtotal = $order->items()->sum('total');
        $tax = $order->items()->get()->sum(fn ($i) => $i->total * ($i->gst_percent / 100));
        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal - $order->discount + $tax,
        ]);
    }

    public function completeOrder(PharmacyOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $order->prescription?->update(['status' => 'dispensed']);

            $visit = $order->patientVisit;
            $visit->update(['status' => VisitStatus::Billing]);

            app(InvoiceService::class)->createInvoice(
                $visit->patient,
                'pharmacy',
                $order->items->map(fn ($i) => [
                    'description' => $i->medicine_name,
                    'quantity' => $i->quantity,
                    'unit_price' => $i->unit_price,
                    'tax' => $i->total * ($i->gst_percent / 100),
                ])->toArray(),
                $visit,
            );

            AuditService::log('pharmacy_completed', $order);

            RealtimeService::queueUpdated(
                'pharmacy_completed',
                $visit->fresh(['patient']),
                "Pharmacy completed — {$visit->patient->name} moved to billing"
            );
        });
    }
}
