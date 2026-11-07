<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use Illuminate\View\View;
use Illuminate\Http\Request;

class LandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Plot::with('location')->where('status', 'available');

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('min_size')) {
            $query->where('size_sqm', '>=', $request->min_size);
        }

        if ($request->filled('max_size')) {
            $query->where('size_sqm', '<=', $request->max_size);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $plots = $query->paginate(12);

        return view('public.land.index', compact('plots'));
    }
}
