<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorAgreement;
use App\Models\InvestorContribution;
use App\Models\InvestorDistribution;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $totalInvested = InvestorContribution::where('investor_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $activeProjects = InvestorAgreement::where('investor_id', $user->id)
            ->where('status', 'active')
            ->count();

        $totalDistributed = InvestorDistribution::where('investor_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $roi = $totalInvested > 0 ? ($totalDistributed / $totalInvested * 100) : 0;

        return view('investor.dashboard', compact('totalInvested', 'activeProjects', 'totalDistributed', 'roi'));
    }
}
