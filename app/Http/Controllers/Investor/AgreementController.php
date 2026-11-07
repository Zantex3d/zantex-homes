<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorAgreement;
use Illuminate\View\View;

class AgreementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $agreements = InvestorAgreement::where('investor_id', $user->id)
            ->paginate(10);

        return view('investor.agreements.index', compact('agreements'));
    }

    public function show($agreementId): View
    {
        $user = auth()->user();
        $agreement = InvestorAgreement::where('id', $agreementId)
            ->where('investor_id', $user->id)
            ->firstOrFail();

        return view('investor.agreements.show', compact('agreement'));
    }
}
