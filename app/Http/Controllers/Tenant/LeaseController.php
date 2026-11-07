<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $lease = Lease::where('tenant_id', $user->id)
            ->where('end_date', '>=', now())
            ->firstOrFail();

        return view('tenant.lease.index', compact('lease'));
    }

    public function downloadPdf()
    {
        $user = auth()->user();
        $lease = Lease::where('tenant_id', $user->id)
            ->where('end_date', '>=', now())
            ->firstOrFail();

        return response()->download(storage_path("app/leases/{$lease->id}.pdf"));
    }
}
