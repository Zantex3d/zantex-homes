<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorDistribution;
use Illuminate\View\View;

class DistributionController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $distributions = InvestorDistribution::where('investor_id', $user->id)
            ->orderBy('date_due', 'desc')
            ->paginate(20);

        return view('investor.distributions.index', compact('distributions'));
    }
}
