@extends('layouts.app')

@section('title', 'Maintenance Requests')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Maintenance Requests</h1>
        <button onclick="document.getElementById('newRequestForm').classList.toggle('hidden')" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">New Request</button>
    </div>

    <!-- New Request Form -->
    <div id="newRequestForm" class="hidden bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6">Submit Maintenance Request</h2>
        <form action="{{ route('tenant.maintenance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2">Category</label>
                <select name="category" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Select Category</option>
                    <option value="plumbing">Plumbing</option>
                    <option value="electrical">Electrical</option>
                    <option value="structural">Structural</option>
                    <option value="cleaning">Cleaning</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Description</label>
                <textarea name="description" rows="4" required class="w-full px-4 py-2 border rounded-lg" placeholder="Describe the maintenance issue..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Upload Photos (Optional)</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="block w-full">
                <p class="text-sm text-slate-600 mt-2">Maximum 5 images, up to 2MB each</p>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Request</button>
        </form>
    </div>

    <!-- Requests List -->
    <div class="space-y-4">
        @forelse ($tickets as $ticket)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg">{{ ucfirst($ticket->category) }} - #{{ $ticket->id }}</h3>
                        <p class="text-slate-600 text-sm">{{ $ticket->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $ticket->status === 'open' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $ticket->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                    ">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
                <p class="text-slate-700 mb-4">{{ $ticket->description }}</p>
                <a href="{{ route('tenant.maintenance.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold">View Details →</a>
            </div>
        @empty
            <div class="bg-slate-50 rounded-lg p-12 text-center">
                <p class="text-slate-600">No maintenance requests yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
