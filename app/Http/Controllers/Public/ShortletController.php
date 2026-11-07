<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ShortletProperty;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ShortletController extends Controller
{
    public function index(Request $request): View
    {
        $query = ShortletProperty::with(['pictures', 'location'])->where('status', 'available');

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('min_price')) {
            $query->where('daily_rate', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        $shortlets = $query->paginate(12);

        return view('public.shortlets.index', compact('shortlets'));
    }
}
