<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(): View
    {
        return view('landlord.payouts.index');
    }

    public function request()
    {
        return back();
    }
}
