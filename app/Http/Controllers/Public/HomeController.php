<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Picture;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProperties = Property::with(['pictures', 'location'])
            ->where('status', 'available')
            ->latest()
            ->limit(6)
            ->get();

        return view('public.home', compact('featuredProperties'));
    }
}
