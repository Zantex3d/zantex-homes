@extends('layouts.public')

@section('title', 'Properties')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold mb-8">Property Search</h1>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <form action="{{ route('properties.search') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Property Type</label>
                        <select name="type" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">All Types</option>
                            <option value="residential" {{ request('type') === 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ request('type') === 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="land" {{ request('type') === 'land' ? 'selected' : '' }}>Land</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Min Price (₦)</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Max Price (₦)</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Bedrooms</label>
                        <select name="bedrooms" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Any</option>
                            <option value="1" {{ request('bedrooms') === '1' ? 'selected' : '' }}>1+</option>
                            <option value="2" {{ request('bedrooms') === '2' ? 'selected' : '' }}>2+</option>
                            <option value="3" {{ request('bedrooms') === '3' ? 'selected' : '' }}>3+</option>
                            <option value="4" {{ request('bedrooms') === '4' ? 'selected' : '' }}>4+</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Search</button>
                    <a href="{{ route('properties.search') }}" class="border border-slate-300 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-50">Clear</a>
                </div>
            </form>
        </div>

        <!-- Results -->
        @if ($properties->count())
            <div class="mb-6 text-slate-600">
                Showing {{ $properties->firstItem() }}-{{ $properties->lastItem() }} of {{ $properties->total() }} properties
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($properties as $property)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="relative h-64 bg-slate-200">
                            @if ($property->pictures()->exists())
                                <img src="{{ Storage::url($property->pictures()->first()->path) }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">No Image</div>
                            @endif
                            @auth
                                <button class="absolute top-3 right-3 bg-white rounded-full p-2 hover:bg-slate-100 shadow">
                                    <form action="{{ route('properties.favorite', $property->slug) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:text-red-600">♥</button>
                                    </form>
                                </button>
                            @endauth
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2">{{ $property->name }}</h3>
                            <p class="text-slate-600 text-sm mb-4">{{ $property->location?->name ?? 'N/A' }}</p>
                            <div class="space-y-2 text-sm mb-4 text-slate-600">
                                <p>{{ $property->bedrooms }} Bed • {{ $property->bathrooms }} Bath • {{ number_format($property->size_sqm) }} m²</p>
                                @if ($property->is_furnished)
                                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">Furnished</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-blue-600">₦{{ number_format($property->asking_price, 0) }}</span>
                                <a href="{{ route('properties.show', $property->slug) }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @else
            <div class="bg-slate-50 rounded-lg p-12 text-center">
                <p class="text-slate-600 text-lg">No properties found. Try adjusting your search criteria.</p>
            </div>
        @endif
    </section>
@endsection
