<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->preferred_company_id;
        
        $properties = Property::where('company_id', $companyId)
            ->with('estate', 'propertyType', 'units')
            ->paginate(15);

        return response()->json($properties);
    }

    public function show(Property $property, Request $request): JsonResponse
    {
        $this->authorize('view', $property);

        return response()->json($property->load('estate', 'propertyType', 'units'));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Property::class);

        $validated = $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'property_code' => 'required|unique:properties',
            'name' => 'required|string|max:255',
            'property_type_id' => 'required|exists:property_types,id',
        ]);

        $property = Property::create(array_merge($validated, [
            'company_id' => $request->user()->preferred_company_id,
            'status' => 'active',
        ]));

        return response()->json($property, 201);
    }

    public function update(Property $property, Request $request): JsonResponse
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'property_type_id' => 'exists:property_types,id',
            'status' => 'in:active,inactive,sold',
        ]);

        $property->update($validated);

        return response()->json($property);
    }

    public function destroy(Property $property, Request $request): JsonResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return response()->json(null, 204);
    }
}
