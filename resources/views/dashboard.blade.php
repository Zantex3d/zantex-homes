@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow border border-slate-200 dark:border-slate-700">
            <p class="text-slate-600 dark:text-slate-400 text-sm">Total Properties</p>
            <p class="text-3xl font-bold mt-2">0</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow border border-slate-200 dark:border-slate-700">
            <p class="text-slate-600 dark:text-slate-400 text-sm">Active Tenants</p>
            <p class="text-3xl font-bold mt-2">0</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow border border-slate-200 dark:border-slate-700">
            <p class="text-slate-600 dark:text-slate-400 text-sm">Pending Payments</p>
            <p class="text-3xl font-bold mt-2 text-blue-600">₦0.00</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow border border-slate-200 dark:border-slate-700">
            <p class="text-slate-600 dark:text-slate-400 text-sm">Account Balance</p>
            <p class="text-3xl font-bold mt-2 text-green-600">₦0.00</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow border border-slate-200 dark:border-slate-700">
        <h2 class="text-lg font-semibold mb-4">Welcome to ZANTEX HOMES</h2>
        <p class="text-slate-600 dark:text-slate-400 mb-4">
            You have successfully logged in. Your dashboard will display key metrics from your real estate operations once data is configured.
        </p>
        <p class="text-slate-500 dark:text-slate-500 text-sm">
            Current timezone: {{ auth()->user()->getActiveCompany()?->getTimezone() }} | 
            Base currency: {{ auth()->user()->getActiveCompany()?->getBaseCurrency() }}
        </p>
    </div>
</div>
@endsection
