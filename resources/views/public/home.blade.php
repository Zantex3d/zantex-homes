@extends('layouts.public')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section class="relative h-96 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto h-full px-4 flex flex-col justify-center">
            <h1 class="text-5xl font-bold mb-4">Find Your Dream Property</h1>
            <p class="text-xl mb-8 max-w-2xl">Discover the perfect home, investment, or business property with Zantex Homes</p>
            
            <!-- Search Widget -->
            <div class="bg-white text-slate-900 p-6 rounded-lg shadow-lg max-w-2xl">
                <form action="{{ route('properties.search') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Property Type</label>
                            <select name="type" class="w-full px-4 py-2 border rounded-lg focus:outline-none">
                                <option value="">All Types</option>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="land">Land</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Min Price (₦)</label>
                            <input type="number" name="min_price" placeholder="0" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Max Price (₦)</label>
                            <input type="number" name="max_price" placeholder="999999999" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Bedrooms</label>
                            <select name="bedrooms" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Any</option>
                                <option value="1">1+</option>
                                <option value="2">2+</option>
                                <option value="3">3+</option>
                                <option value="4">4+</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
                        Search Properties
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Properties -->
    <section class="max-w-7xl mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold mb-8">Featured Properties</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($featuredProperties as $property)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative h-48 bg-slate-200">
                        @if ($property->pictures()->exists())
                            <img src="{{ Storage::url($property->pictures()->first()->path) }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">No Image</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">{{ $property->name }}</h3>
                        <p class="text-slate-600 text-sm mb-4">{{ $property->location?->name ?? 'N/A' }}</p>
                        <div class="space-y-2 text-sm mb-4">
                            <p>{{ $property->bedrooms }} Bedrooms • {{ $property->bathrooms }} Bathrooms</p>
                            <p>{{ number_format($property->size_sqm) }} m²</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-blue-600">₦{{ number_format($property->asking_price, 0) }}</span>
                            <a href="{{ route('properties.show', $property->slug) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">View</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CTA Sections -->
    <section class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-blue-50 p-8 rounded-lg text-center">
                <h3 class="text-xl font-bold mb-4">Buy a Property</h3>
                <p class="text-slate-600 mb-6">Browse our extensive collection of residential and commercial properties</p>
                <a href="{{ route('properties.search') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Browse Now</a>
            </div>
            <div class="bg-green-50 p-8 rounded-lg text-center">
                <h3 class="text-xl font-bold mb-4">Make an Investment</h3>
                <p class="text-slate-600 mb-6">Explore lucrative investment opportunities with excellent returns</p>
                <a href="{{ route('investments') }}" class="inline-block bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Learn More</a>
            </div>
            <div class="bg-orange-50 p-8 rounded-lg text-center">
                <h3 class="text-xl font-bold mb-4">Short-term Rental</h3>
                <p class="text-slate-600 mb-6">Find furnished properties perfect for short-term stays</p>
                <a href="{{ route('shortlets.index') }}" class="inline-block bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700">Explore</a>
            </div>
        </div>
    </section>
@endsection
