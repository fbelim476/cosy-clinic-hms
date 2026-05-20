<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['patient', 'patientVisit'])
            ->latest()
            ->paginate(20);

        $todayRevenue = Invoice::whereDate('created_at', today())->sum('paid_amount');

        return view('pages.billing.dashboard', compact('invoices', 'todayRevenue'));
    }

    public function payment(Request $request, Invoice $invoice, InvoiceService $service)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,upi,online,other',
            'transaction_ref' => 'nullable|string|max:100',
        ]);

        $service->recordPayment($invoice, $data['amount'], $data['method'], $data['transaction_ref'] ?? null);

        return back()->with('success', 'Payment recorded successfully.');
    }
}
