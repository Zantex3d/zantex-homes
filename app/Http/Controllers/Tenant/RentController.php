<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\RentInvoice;
use App\Models\Payment;
use App\Models\ServiceCharge;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RentController extends Controller
{
    public function invoices(Request $request): View
    {
        $user = auth()->user();
        $query = RentInvoice::where('tenant_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        $invoices = $query->orderBy('due_date', 'desc')->paginate(20);

        return view('tenant.rent.invoices', compact('invoices'));
    }

    public function payments(Request $request): View
    {
        $user = auth()->user();
        $payments = Payment::where('payer_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        return view('tenant.rent.payments', compact('payments'));
    }

    public function serviceCharges(Request $request): View
    {
        $user = auth()->user();
        $lease = $user->lease()->where('end_date', '>=', now())->first();

        $charges = ServiceCharge::where('unit_id', $lease->unit_id ?? null)
            ->orderBy('due_date', 'desc')
            ->paginate(20);

        return view('tenant.rent.service-charges', compact('charges'));
    }

    public function initiatePayment(Request $request, $invoiceId): RedirectResponse
    {
        $user = auth()->user();
        $invoice = RentInvoice::where('id', $invoiceId)
            ->where('tenant_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // TODO: Integrate with Paystack
        // For now, redirect to payment gateway

        return redirect()->to("https://checkout.paystack.com/invoice/{$invoice->paystack_reference}")
            ->with('info', 'Redirecting to payment gateway...');
    }
}
