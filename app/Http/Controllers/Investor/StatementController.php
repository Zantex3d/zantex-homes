<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StatementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        return view('investor.statements', compact('user'));
    }

    public function download()
    {
        // TODO: Generate PDF statement
        return back();
    }
}
