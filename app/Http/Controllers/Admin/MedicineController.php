<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with('batches')->orderBy('name')->paginate(20);

        return view('pages.admin.medicines', compact('medicines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string',
            'sku' => 'nullable|string|unique:medicines,sku',
            'selling_price' => 'required|numeric|min:0',
            'gst_percent' => 'nullable|numeric',
            'reorder_level' => 'nullable|integer',
        ]);

        Medicine::create($data);

        return back()->with('success', 'Medicine added.');
    }
}
