<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\RentInvoice;
use App\Models\MaintenanceTicket;
use App\Models\Expense;
use Illuminate\View\View;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $period = $request->period ?? 'month';
        $properties = $user->properties();

        $rentCollected = RentInvoice::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'paid')
            ->sum('amount');

        $fees = Expense::whereIn('property_id', $properties->pluck('id'))
            ->where('category', 'management_fee')
            ->where('status', 'approved')
            ->sum('amount');

        $maintenance = MaintenanceTicket::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'completed')
            ->sum('estimated_cost');

        $netPayout = $rentCollected - $fees - $maintenance;

        return view('landlord.statements', compact(
            'period',
            'rentCollected',
            'fees',
            'maintenance',
            'netPayout'
        ));
    }

    public function download(Request $request)
    {
        // TODO: Generate PDF statement
        return back();
    }
}
