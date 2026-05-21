<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MedicinesTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                'Paracetamol 500mg', 'Acetaminophen', 'MED001', '8901234567890',
                'Tablet', 'Tablet', 'ABC Pharma', '500mg', 'strip',
                25, 18, 5, 500, 10, 'active', 'Sample row — delete before import',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'medicine_name', 'generic_name', 'sku', 'barcode', 'category', 'medicine_type',
            'manufacturer', 'strength', 'unit', 'price', 'purchase_price', 'gst_percent',
            'stock', 'reorder_level', 'status', 'description',
        ];
    }
}
