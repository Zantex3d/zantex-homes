@extends('layouts.app')

@section('title', 'Tenant Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Tenant Dashboard</h1>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-slate-600 text-sm mb-2">Monthly Rent</p>
            @if ($lease)
                <p class="text-3xl font-bold text-blue-600">₦{{ number_format($lease->monthly_amount, 2) }}</p>
                <p class="text-slate-600 text-sm mt-2">Next Payment: {{ $lease->start_date->format('M d, Y') }}</p>
            @else
                <p class="text-2xl font-bold text-slate-400">No Active Lease</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-slate-600 text-sm mb-2">Next Rent Due</p>
            @if ($nextInvoice)
                <p class="text-3xl font-bold text-orange-600">₦{{ number_format($nextInvoice->amount, 2) }}</p>
                <p class="text-slate-600 text-sm mt-2">Due: {{ $nextInvoice->due_date->format('M d, Y') }}</p>
            @else
                <p class="text-2xl font-bold text-green-600">No Outstanding Invoices</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-slate-600 text-sm mb-2">Maintenance Requests</p>
            <p class="text-3xl font-bold text-slate-900">{{ $openTickets }}</p>
            <p class="text-slate-600 text-sm mt-2">Open/In Progress</p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('tenant.lease') }}" class="bg-blue-50 p-6 rounded-lg hover:shadow-md transition text-center">
            <p class="text-2xl mb-2">📄</p>
            <h3 class="font-bold mb-2">View Lease</h3>
            <p class="text-slate-600 text-sm">Details and terms</p>
        </a>
        <a href="{{ route('tenant.rent.invoices') }}" class="bg-green-50 p-6 rounded-lg hover:shadow-md transition text-center">
            <p class="text-2xl mb-2">💳</p>
            <h3 class="font-bold mb-2">Pay Rent</h3>
            <p class="text-slate-600 text-sm">Invoices & payments</p>
        </a>
        <a href="{{ route('tenant.maintenance.index') }}" class="bg-orange-50 p-6 rounded-lg hover:shadow-md transition text-center">
            <p class="text-2xl mb-2">🔧</p>
            <h3 class="font-bold mb-2">Maintenance</h3>
            <p class="text-slate-600 text-sm">Submit requests</p>
        </a>
        <a href="{{ route('tenant.documents') }}" class="bg-purple-50 p-6 rounded-lg hover:shadow-md transition text-center">
            <p class="text-2xl mb-2">📁</p>
            <h3 class="font-bold mb-2">Documents</h3>
            <p class="text-slate-600 text-sm">Receipts & files</p>
        </a>
    </div>

    <!-- Lease Information -->
    @if ($lease)
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6">Current Lease</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-slate-600 text-sm mb-2">Property</p>
                <p class="font-bold">{{ $lease->property?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Unit</p>
                <p class="font-bold">{{ $lease->unit?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Lease Start</p>
                <p class="font-bold">{{ $lease->start_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-slate-600 text-sm mb-2">Lease End</p>
                <p class="font-bold">{{ $lease->end_date->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
