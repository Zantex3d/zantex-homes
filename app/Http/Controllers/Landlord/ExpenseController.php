<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        return view('landlord.expenses.index');
    }

    public function pending(): View
    {
        return view('landlord.expenses.pending');
    }

    public function approve($expenseId)
    {
        return back();
    }
}
