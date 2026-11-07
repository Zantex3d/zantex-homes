@extends('layouts.app')

@section('title', 'Rent Invoices')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Rent Invoices</h1>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" class="flex gap-4">
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Filter</button>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-100 border-b">
                <tr>
                    <th class="px-6 py-4 text-left font-bold">Invoice #</th>
                    <th class="px-6 py-4 text-left font-bold">Amount</th>
                    <th class="px-6 py-4 text-left font-bold">Due Date</th>
                    <th class="px-6 py-4 text-left font-bold">Status</th>
                    <th class="px-6 py-4 text-left font-bold">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="px-6 py-4">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4 font-bold">₦{{ number_format($invoice->amount, 2) }}</td>
                        <td class="px-6 py-4">{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($invoice->status === 'pending')
                                <form action="{{ route('tenant.rent.pay', $invoice->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-700 font-semibold">Pay Now</button>
                                </form>
                            @else
                                <a href="#" class="text-slate-600 hover:text-slate-700 font-semibold">Receipt</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-600">No invoices found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
