<?php

namespace App\Services;

use App\Enums\VisitStatus;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Models\Medicine;
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
                    $resolved = app(MedicineService::class)->resolveForPrescriptionItem($item);
                    $qty = $item->quantity;
                    $price = $resolved['unit_price'];
                    $total = $price * $qty;

                    PharmacyOrderItem::create([
                        'pharmacy_order_id' => $order->id,
                        'medicine_id' => $resolved['medicine_id'],
                        'prescription_item_id' => $item->id,
                        'medicine_name' => $item->medicine_name,
                        'sku' => $resolved['sku'],
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'gst_percent' => $resolved['gst_percent'],
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

    public function recalculateLineItem(PharmacyOrderItem $item): PharmacyOrderItem
    {
        $lineTotal = max(0, ($item->unit_price * $item->quantity) - ($item->discount ?? 0));
        $item->update(['total' => $lineTotal]);

        return $item->fresh();
    }

    public function recalculateOrder(PharmacyOrder $order): PharmacyOrder
    {
        $items = $order->items()->get();
        $subtotal = $items->sum('total');
        $tax = $items->sum(fn ($i) => $i->total * ($i->gst_percent / 100));
        $order->update([
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($subtotal - $order->discount + $tax, 2),
        ]);

        return $order->fresh(['items', 'patientVisit.patient']);
    }

    public function updateItemQuantity(PharmacyOrderItem $item, int $quantity): PharmacyOrder
    {
        $item->update(['quantity' => max(1, $quantity)]);
        $this->recalculateLineItem($item);

        return $this->recalculateOrder($item->pharmacyOrder);
    }

    public function updateItemDiscount(PharmacyOrderItem $item, float $discount): PharmacyOrder
    {
        $item->update(['discount' => max(0, $discount)]);
        $this->recalculateLineItem($item);

        return $this->recalculateOrder($item->pharmacyOrder);
    }

    public function updateOrderDiscount(PharmacyOrder $order, float $discount): PharmacyOrder
    {
        $order->update(['discount' => max(0, $discount)]);

        return $this->recalculateOrder($order);
    }

    public function addOtcItem(
        PharmacyOrder $order,
        string $name,
        float $unitPrice,
        int $quantity,
        float $gstPercent = 0,
        ?int $medicineId = null,
        ?string $sku = null,
        ?string $notes = null,
    ): PharmacyOrder {
        $qty = max(1, $quantity);
        $total = $unitPrice * $qty;

        PharmacyOrderItem::create([
            'pharmacy_order_id' => $order->id,
            'medicine_id' => $medicineId,
            'medicine_name' => $name,
            'sku' => $sku,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'gst_percent' => $gstPercent,
            'total' => $total,
            'is_otc' => true,
            'notes' => $notes,
        ]);

        return $this->recalculateOrder($order);
    }

    public function addOtcFromMedicine(PharmacyOrder $order, Medicine $medicine, int $quantity = 1): PharmacyOrder
    {
        return $this->addOtcItem(
            $order,
            $medicine->name,
            (float) $medicine->selling_price,
            $quantity,
            (float) ($medicine->gst_percent ?? 0),
            $medicine->id,
            $medicine->sku,
        );
    }

    public function updateItemPrice(PharmacyOrderItem $item, float $unitPrice): PharmacyOrder
    {
        $item->update(['unit_price' => max(0, $unitPrice)]);
        $this->recalculateLineItem($item);

        return $this->recalculateOrder($item->pharmacyOrder);
    }

    public function updateItemGst(PharmacyOrderItem $item, float $gstPercent): PharmacyOrder
    {
        $item->update(['gst_percent' => max(0, $gstPercent)]);
        $this->recalculateLineItem($item);

        return $this->recalculateOrder($item->pharmacyOrder);
    }

    public function updateItemNotes(PharmacyOrderItem $item, ?string $notes): PharmacyOrder
    {
        $item->update(['notes' => $notes]);

        return $item->pharmacyOrder->fresh(['items', 'patientVisit.patient']);
    }

    public function removeItem(PharmacyOrderItem $item): PharmacyOrder
    {
        $order = $item->pharmacyOrder;
        $item->delete();

        return $this->recalculateOrder($order);
    }

    public function replaceItemWithMedicine(PharmacyOrderItem $item, Medicine $medicine): PharmacyOrder
    {
        $item->update([
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name,
            'sku' => $medicine->sku,
            'unit_price' => $medicine->selling_price,
            'gst_percent' => $medicine->gst_percent ?? 0,
        ]);
        $this->recalculateLineItem($item);

        return $this->recalculateOrder($item->pharmacyOrder);
    }

    public function completeOrder(PharmacyOrder $order, ?float $paidAmount = null, string $paymentMethod = 'cash'): void
    {
        DB::transaction(function () use ($order, $paidAmount, $paymentMethod) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $order->prescription?->update(['status' => 'dispensed']);

            $visit = $order->patientVisit;
            $visit->update(['status' => VisitStatus::Billing]);

            $invoice = app(InvoiceService::class)->createInvoice(
                $visit->patient,
                'pharmacy',
                $order->items->map(fn ($i) => [
                    'description' => $i->medicine_name,
                    'quantity' => $i->quantity,
                    'unit_price' => $i->unit_price,
                    'tax' => round($i->total * ($i->gst_percent / 100), 2),
                ])->toArray(),
                $visit,
                $order->discount,
            );

            if ($paidAmount !== null && $paidAmount > 0) {
                app(InvoiceService::class)->recordPayment($invoice, $paidAmount, $paymentMethod);
            }

            AuditService::log('pharmacy_completed', $order);

            RealtimeService::queueUpdated(
                'pharmacy_completed',
                $visit->fresh(['patient']),
                "Pharmacy completed — {$visit->patient->name} moved to billing"
            );
        });
    }
}
