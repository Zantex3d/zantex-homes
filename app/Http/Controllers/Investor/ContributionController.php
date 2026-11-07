<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorContribution;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ContributionController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $contributions = InvestorContribution::where('investor_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('investor.contributions.index', compact('contributions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:development_projects,id',
            'amount' => 'required|numeric|min:1',
        ]);

        InvestorContribution::create([
            'investor_id' => auth()->id(),
            ...$validated,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Contribution submitted for approval!');
    }
}
