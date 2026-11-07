@extends('layouts.app')

@section('title', 'My Lease')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Lease Agreement</h1>
        <a href="{{ route('tenant.lease.download') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Download PDF</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-slate-600 text-sm mb-2">Tenant Name</p>
                <p class="font-bold text-lg">{{ $lease->tenant?->name }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Property</p>
                <p class="font-bold text-lg">{{ $lease->property?->name }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Unit</p>
                <p class="font-bold text-lg">{{ $lease->unit?->name }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Landlord</p>
                <p class="font-bold text-lg">{{ $lease->landlord?->name }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Lease Start Date</p>
                <p class="font-bold text-lg">{{ $lease->start_date->format('F d, Y') }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Lease End Date</p>
                <p class="font-bold text-lg">{{ $lease->end_date->format('F d, Y') }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Monthly Rent</p>
                <p class="font-bold text-lg text-blue-600">₦{{ number_format($lease->monthly_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Lease Period</p>
                <p class="font-bold text-lg">{{ $lease->duration_months }} Months</p>
            </div>
        </div>

        <div class="border-t pt-8">
            <h2 class="text-xl font-bold mb-4">Lease Terms</h2>
            <div class="prose prose-sm max-w-none text-slate-700">
                {!! nl2br($lease->terms) !!}
            </div>
        </div>
    </div>
</div>
@endsection
