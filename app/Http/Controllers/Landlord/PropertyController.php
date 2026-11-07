<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use App\Models\RentInvoice;
use App\Models\MaintenanceTicket;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $properties = Property::where('landlord_id', $user->id)
            ->with('units')
            ->paginate(12);

        return view('landlord.properties.index', compact('properties'));
    }

    public function show($propertyId): View
    {
        $user = auth()->user();
        $property = Property::where('id', $propertyId)
            ->where('landlord_id', $user->id)
            ->with('units.lease')
            ->firstOrFail();

        $units = $property->units;
        $occupied = $units->where('status', 'occupied')->count();
        $vacant = $units->where('status', 'vacant')->count();

        $monthlyRent = $units->sum('monthly_rent');
        $collected = RentInvoice::where('property_id', $property->id)
            ->where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $maintenance = MaintenanceTicket::where('property_id', $property->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('landlord.properties.show', compact(
            'property',
            'occupied',
            'vacant',
            'monthlyRent',
            'collected',
            'maintenance'
        ));
    }
}
