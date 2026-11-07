<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    public function index(): View
    {
        return view('agent.performance');
    }
}
