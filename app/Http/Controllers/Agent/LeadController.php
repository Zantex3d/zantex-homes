<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function index(): View
    {
        return view('agent.leads.index');
    }

    public function show($leadId): View
    {
        return view('agent.leads.show');
    }

    public function addNote(Request $request, $leadId): RedirectResponse
    {
        return back()->with('success', 'Note added successfully!');
    }

    public function schedule(Request $request, $leadId): RedirectResponse
    {
        return back()->with('success', 'Viewing scheduled!');
    }
}
