@extends('layouts.public')

@section('title', 'Shortlets')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold mb-8">Short-term Rentals</h1>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <form action="{{ route('shortlets.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Check-in</label>
                        <input type="date" name="checkin" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Check-out</label>
                        <input type="date" name="checkout" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Min Price (₦/night)</label>
                        <input type="number" name="min_price" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Max Price (₦/night)</label>
                        <input type="number" name="max_price" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Search</button>
            </form>
        </div>

        <!-- Results -->
        @if ($shortlets->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($shortlets as $shortlet)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="h-48 bg-slate-200">
                            @if ($shortlet->pictures()->exists())
                                <img src="{{ Storage::url($shortlet->pictures()->first()->path) }}" alt="{{ $shortlet->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2">{{ $shortlet->name }}</h3>
                            <p class="text-slate-600 text-sm mb-4">{{ $shortlet->location?->name }}</p>
                            <div class="mb-4">
                                <span class="text-2xl font-bold text-blue-600">₦{{ number_format($shortlet->daily_rate, 0) }}</span>
                                <span class="text-slate-600">/night</span>
                            </div>
                            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Book Now</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $shortlets->links() }}
            </div>
        @else
            <div class="bg-slate-50 rounded-lg p-12 text-center">
                <p class="text-slate-600 text-lg">No shortlets available.</p>
            </div>
        @endif
    </section>
@endsection
