<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceTicket;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = MaintenanceTicket::where('tenant_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('tenant.maintenance.index', compact('tickets'));
    }

    public function show($ticketId): View
    {
        $user = auth()->user();
        $ticket = MaintenanceTicket::where('id', $ticketId)
            ->where('tenant_id', $user->id)
            ->firstOrFail();

        return view('tenant.maintenance.show', compact('ticket'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|max:2048',
        ]);

        $ticket = MaintenanceTicket::create([
            'tenant_id' => $user->id,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("maintenance/{$ticket->id}", 'public');
                $ticket->pictures()->create(['path' => $path]);
            }
        }

        return redirect()->route('tenant.maintenance.show', $ticket->id)
            ->with('success', 'Maintenance request submitted successfully!');
    }
}
