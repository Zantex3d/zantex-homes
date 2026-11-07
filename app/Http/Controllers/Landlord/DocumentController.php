<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        return view('landlord.documents');
    }
}
