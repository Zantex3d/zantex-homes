<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorContribution;
use App\Models\InvestorDistribution;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $period = $request->period ?? 'ytd';

        $totalInvested = InvestorContribution::where('investor_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $totalDistributed = InvestorDistribution::where('investor_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $roi = $totalInvested > 0 ? (($totalDistributed / $totalInvested) * 100) : 0;

        return view('investor.performance', compact('period', 'totalInvested', 'totalDistributed', 'roi'));
    }
}
