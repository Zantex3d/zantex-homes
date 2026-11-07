<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCommission;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $period = $request->period ?? 'month';

        $commissions = AgentCommission::where('agent_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('agent.commissions', compact('commissions', 'period'));
    }
}
