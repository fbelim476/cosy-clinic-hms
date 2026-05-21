<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MedicinesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Medicine::with('batches')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'medicine_name', 'generic_name', 'sku', 'barcode', 'category', 'medicine_type',
            'manufacturer', 'strength', 'unit', 'price', 'purchase_price', 'gst_percent',
            'stock', 'reorder_level', 'status', 'description',
        ];
    }

    public function map($medicine): array
    {
        return [
            $medicine->name,
            $medicine->generic_name,
            $medicine->sku,
            $medicine->barcode,
            $medicine->category,
            $medicine->medicine_type,
            $medicine->manufacturer,
            $medicine->strength,
            $medicine->unit,
            $medicine->selling_price,
            $medicine->purchase_price,
            $medicine->gst_percent,
            $medicine->stockQuantity(),
            $medicine->reorder_level,
            $medicine->is_active ? 'active' : 'inactive',
            $medicine->description,
        ];
    }
}
