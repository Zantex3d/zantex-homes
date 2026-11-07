<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\RentInvoice;
use App\Models\Payment;
use App\Models\MaintenanceTicket;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $lease = Lease::where('tenant_id', $user->id)
            ->where('end_date', '>=', now())
            ->first();

        $nextInvoice = RentInvoice::where('tenant_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();

        $openTickets = MaintenanceTicket::where('tenant_id', $user->id)
            ->whereNotIn('status', ['completed', 'rejected'])
            ->count();

        return view('tenant.dashboard', compact('lease', 'nextInvoice', 'openTickets'));
    }
}
