<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use App\Models\RentInvoice;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $properties = Property::where('landlord_id', $user->id)->get();

        $totalProperties = $properties->count();
        $occupiedUnits = Unit::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'occupied')
            ->count();
        $vacantUnits = Unit::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'vacant')
            ->count();

        $monthlyCollection = Payment::where('payee_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $arrears = RentInvoice::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'overdue')
            ->sum('amount');

        return view('landlord.dashboard', compact(
            'totalProperties',
            'occupiedUnits',
            'vacantUnits',
            'monthlyCollection',
            'arrears'
        ));
    }
}
