@extends('layouts.public')

@section('title', 'Land & Plots')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold mb-8">Land & Plots for Sale</h1>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <form action="{{ route('land.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Min Size (m²)</label>
                        <input type="number" name="min_size" value="{{ request('min_size') }}" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Max Size (m²)</label>
                        <input type="number" name="max_size" value="{{ request('max_size') }}" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results -->
        @if ($plots->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($plots as $plot)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-lg">{{ $plot->name }}</h3>
                                <p class="text-slate-600">{{ $plot->location?->name }}</p>
                            </div>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">{{ ucfirst($plot->status) }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b">
                            <div>
                                <p class="text-slate-600 text-sm">Size</p>
                                <p class="font-bold">{{ number_format($plot->size_sqm) }} m²</p>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm">Price</p>
                                <p class="font-bold text-blue-600">₦{{ number_format($plot->price, 0) }}</p>
                            </div>
                        </div>
                        <p class="text-slate-700 text-sm mb-4">{{ Str::limit($plot->description, 100) }}</p>
                        <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Inquire Now</button>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $plots->links() }}
            </div>
        @else
            <div class="bg-slate-50 rounded-lg p-12 text-center">
                <p class="text-slate-600 text-lg">No plots available matching your criteria.</p>
            </div>
        @endif
    </section>
@endsection
