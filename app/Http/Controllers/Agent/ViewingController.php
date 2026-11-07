<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ViewingController extends Controller
{
    public function index(): View
    {
        return view('agent.viewings.index');
    }

    public function show($viewingId): View
    {
        return view('agent.viewings.show');
    }

    public function storeFeedback(Request $request, $viewingId): RedirectResponse
    {
        return back()->with('success', 'Feedback recorded!');
    }

    public function complete(Request $request, $viewingId): RedirectResponse
    {
        return back()->with('success', 'Viewing marked as complete!');
    }
}
