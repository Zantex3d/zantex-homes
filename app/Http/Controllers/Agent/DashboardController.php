<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCommission;
use App\Models\PropertyViewing;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $thisMonthSales = AgentCommission::where('agent_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $ytdSales = AgentCommission::where('agent_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $pendingCommissions = AgentCommission::where('agent_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $leadsCount = 0; // TODO: Get from PartyInteractions/Leads table

        return view('agent.dashboard', compact(
            'thisMonthSales',
            'ytdSales',
            'pendingCommissions',
            'leadsCount'
        ));
    }
}
