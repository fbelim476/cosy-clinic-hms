<?php

namespace App\Imports;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicinesImport implements ToCollection, WithHeadingRow
{
    public array $imported = [];
    public array $skipped = [];
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $name = trim((string) ($row['medicine_name'] ?? $row['name'] ?? ''));

            if ($name === '') {
                $this->skipped[] = ['line' => $line, 'reason' => 'Empty medicine name'];
                continue;
            }

            $sku = trim((string) ($row['sku'] ?? '')) ?: null;

            if ($sku && Medicine::where('sku', $sku)->exists()) {
                $this->skipped[] = ['line' => $line, 'reason' => "Duplicate SKU: {$sku}"];
                continue;
            }

            try {
                $medicine = Medicine::create([
                    'name' => $name,
                    'generic_name' => $row['generic_name'] ?? null,
                    'sku' => $sku,
                    'barcode' => $row['barcode'] ?? null,
                    'category' => $row['category'] ?? null,
                    'medicine_type' => $row['medicine_type'] ?? $row['type'] ?? null,
                    'manufacturer' => $row['manufacturer'] ?? null,
                    'unit' => $row['unit'] ?? 'strip',
                    'strength' => $row['strength'] ?? null,
                    'selling_price' => (float) ($row['price'] ?? $row['selling_price'] ?? 0),
                    'purchase_price' => (float) ($row['purchase_price'] ?? 0),
                    'gst_percent' => (float) ($row['gst_percent'] ?? $row['gst'] ?? 0),
                    'reorder_level' => (int) ($row['reorder_level'] ?? 10),
                    'description' => $row['description'] ?? null,
                    'is_active' => ! in_array(strtolower((string) ($row['status'] ?? 'active')), ['inactive', '0', 'no'], true),
                ]);

                $stock = (int) ($row['stock'] ?? 0);
                if ($stock > 0) {
                    MedicineBatch::create([
                        'medicine_id' => $medicine->id,
                        'batch_number' => $row['batch_no'] ?? $row['batch_number'] ?? 'IMP-' . $medicine->id,
                        'expiry_date' => $row['expiry_date'] ?? now()->addYear()->toDateString(),
                        'quantity' => $stock,
                        'purchase_price' => $medicine->purchase_price,
                        'selling_price' => $medicine->selling_price,
                    ]);
                }

                $this->imported[] = $name;
            } catch (\Throwable $e) {
                $this->errors[] = ['line' => $line, 'reason' => $e->getMessage()];
            }
        }
    }
}
