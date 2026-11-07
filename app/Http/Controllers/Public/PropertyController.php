<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Picture;
use App\Models\PropertyInquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{
    public function search(Request $request): View
    {
        $query = Property::with(['pictures', 'location'])->where('status', 'available');

        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('asking_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('asking_price', '<=', $request->max_price);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->filled('furnished')) {
            $query->where('is_furnished', $request->furnished);
        }

        $properties = $query->paginate(12);

        return view('public.properties.search', compact('properties'));
    }

    public function show($slug): View
    {
        $property = Property::with(['pictures', 'location', 'building'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProperties = Property::with(['pictures', 'location'])
            ->where('location_id', $property->location_id)
            ->where('property_type', $property->property_type)
            ->where('id', '!=', $property->id)
            ->limit(4)
            ->get();

        return view('public.properties.show', compact('property', 'relatedProperties'));
    }

    public function storeInquiry(Request $request, $slug): RedirectResponse
    {
        $property = Property::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        PropertyInquiry::create([
            'property_id' => $property->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return back()->with('success', 'Inquiry submitted successfully!');
    }

    public function toggleFavorite(Request $request, $slug): RedirectResponse
    {
        $property = Property::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if ($user->favorites()->where('property_id', $property->id)->exists()) {
            $user->favorites()->detach($property->id);
        } else {
            $user->favorites()->attach($property->id);
        }

        return back();
    }
}
