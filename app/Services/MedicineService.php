<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\PrescriptionItem;
use Illuminate\Support\Facades\DB;

class MedicineService
{
    public function resolveForPrescriptionItem(PrescriptionItem $item): array
    {
        $medicine = $item->medicine
            ?? ($item->medicine_id ? Medicine::find($item->medicine_id) : null)
            ?? Medicine::where('is_active', true)->where('name', $item->medicine_name)->first();

        $price = (float) ($item->unit_price > 0 ? $item->unit_price : ($medicine?->selling_price ?? 0));
        $gst = (float) ($item->gst_percent > 0 ? $item->gst_percent : ($medicine?->gst_percent ?? 0));

        return [
            'medicine' => $medicine,
            'medicine_id' => $medicine?->id ?? $item->medicine_id,
            'unit_price' => $price,
            'gst_percent' => $gst,
            'sku' => $item->sku ?? $medicine?->sku,
            'stock' => $medicine ? $medicine->stockQuantity() : 0,
        ];
    }

    public function snapshotFromMedicine(?Medicine $medicine, string $fallbackName): array
    {
        if (! $medicine) {
            return [
                'medicine_id' => null,
                'medicine_name' => $fallbackName,
                'unit_price' => 0,
                'gst_percent' => 0,
                'sku' => null,
                'stock' => 0,
            ];
        }

        return [
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name,
            'unit_price' => (float) $medicine->selling_price,
            'gst_percent' => (float) ($medicine->gst_percent ?? 0),
            'sku' => $medicine->sku,
            'stock' => $medicine->stockQuantity(),
        ];
    }

    public function store(array $data, ?int $initialStock = null): Medicine
    {
        return DB::transaction(function () use ($data, $initialStock) {
            $medicine = Medicine::create($data);

            if ($initialStock && $initialStock > 0) {
                MedicineBatch::create([
                    'medicine_id' => $medicine->id,
                    'batch_number' => $data['batch_number'] ?? 'INIT-' . $medicine->id,
                    'expiry_date' => $data['expiry_date'] ?? now()->addYear()->toDateString(),
                    'quantity' => $initialStock,
                    'purchase_price' => $data['purchase_price'] ?? $medicine->purchase_price,
                    'selling_price' => $medicine->selling_price,
                    'branch_id' => $data['branch_id'] ?? null,
                ]);
            }

            return $medicine;
        });
    }

    public function update(Medicine $medicine, array $data): Medicine
    {
        $medicine->update($data);

        return $medicine->fresh();
    }

    public function softDelete(Medicine $medicine): void
    {
        $medicine->delete();
    }

    public function restore(int $id): Medicine
    {
        $medicine = Medicine::withTrashed()->findOrFail($id);
        $medicine->restore();

        return $medicine;
    }
}
