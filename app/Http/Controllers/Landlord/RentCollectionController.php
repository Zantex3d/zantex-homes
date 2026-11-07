<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\RentInvoice;
use App\Models\Payment;
use Illuminate\View\View;
use Illuminate\Http\Request;

class RentCollectionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $properties = $user->properties();

        $invoicesDue = RentInvoice::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->paginate(20);

        $paymentsReceived = Payment::where('payee_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        $arrears = RentInvoice::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'overdue')
            ->where('due_date', '<', now()->subDays(30))
            ->paginate(10);

        return view('landlord.rent-collection', compact('invoicesDue', 'paymentsReceived', 'arrears'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        // TODO: Export to Excel/PDF
        return back();
    }
}
