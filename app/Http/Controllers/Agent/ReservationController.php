<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(): View
    {
        return view('agent.reservations.index');
    }

    public function show($reservationId): View
    {
        return view('agent.reservations.show');
    }
}
