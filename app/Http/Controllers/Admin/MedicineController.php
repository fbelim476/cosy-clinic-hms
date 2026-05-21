<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MedicinesExport;
use App\Exports\MedicinesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MedicinesImport;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MedicineController extends Controller
{
    public function index()
    {
        return view('pages.admin.medicines');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string',
            'sku' => 'nullable|string|unique:medicines,sku',
            'barcode' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'gst_percent' => 'nullable|numeric',
            'reorder_level' => 'nullable|integer',
        ]);

        Medicine::create($data);

        return back()->with('success', 'Medicine added.');
    }

    public function export()
    {
        return Excel::download(new MedicinesExport, 'medicines-' . date('Y-m-d') . '.xlsx');
    }

    public function template()
    {
        return Excel::download(new MedicinesTemplateExport, 'medicines-import-template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        $import = new MedicinesImport;
        Excel::import($import, $request->file('file'));

        return back()->with('import_report', [
            'imported' => count($import->imported),
            'skipped' => count($import->skipped),
            'errors' => count($import->errors),
            'details' => [
                'imported' => $import->imported,
                'skipped' => $import->skipped,
                'errors' => $import->errors,
            ],
        ]);
    }
}
