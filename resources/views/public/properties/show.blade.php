@extends('layouts.public')

@section('title', $property->name)

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Gallery -->
                <div class="mb-8">
                    @if ($property->pictures()->exists())
                        <div class="relative h-96 bg-slate-200 rounded-lg overflow-hidden mb-4">
                            <img id="mainImage" src="{{ Storage::url($property->pictures()->first()->path) }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($property->pictures() as $picture)
                                <img src="{{ Storage::url($picture->path) }}" alt="{{ $property->name }}" class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75" onclick="document.getElementById('mainImage').src = this.src">
                            @endforeach
                        </div>
                    @else
                        <div class="h-96 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400">No Images Available</div>
                    @endif
                </div>

                <!-- Property Details -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h1 class="text-3xl font-bold mb-4">{{ $property->name }}</h1>
                    <p class="text-slate-600 mb-6">{{ $property->location?->name ?? 'N/A' }}</p>

                    <div class="grid grid-cols-4 gap-4 mb-8 pb-8 border-b">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $property->bedrooms }}</p>
                            <p class="text-slate-600 text-sm">Bedrooms</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $property->bathrooms }}</p>
                            <p class="text-slate-600 text-sm">Bathrooms</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($property->size_sqm) }}</p>
                            <p class="text-slate-600 text-sm">m²</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $property->year_built }}</p>
                            <p class="text-slate-600 text-sm">Year Built</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">About This Property</h2>
                        <p class="text-slate-700 leading-relaxed">{{ $property->description }}</p>
                    </div>

                    <!-- Features -->
                    @if ($property->features)
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4">Features & Amenities</h2>
                            <ul class="grid grid-cols-2 gap-4">
                                @foreach (explode(',', $property->features) as $feature)
                                    <li class="flex items-center">
                                        <span class="text-green-600 mr-2">✓</span>
                                        {{ trim($feature) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Related Properties -->
                @if ($relatedProperties->count())
                    <div class="mt-16">
                        <h2 class="text-2xl font-bold mb-6">Similar Properties</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ($relatedProperties as $related)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                    <div class="relative h-48 bg-slate-200">
                                        @if ($related->pictures()->exists())
                                            <img src="{{ Storage::url($related->pictures()->first()->path) }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg mb-2">{{ $related->name }}</h3>
                                        <p class="text-slate-600 text-sm mb-4">{{ $related->location?->name }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xl font-bold text-blue-600">₦{{ number_format($related->asking_price, 0) }}</span>
                                            <a href="{{ route('properties.show', $related->slug) }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Price Card -->
                <div class="bg-blue-50 rounded-lg p-6 mb-6 sticky top-4">
                    <div class="mb-6">
                        <p class="text-slate-600 text-sm mb-2">Asking Price</p>
                        <p class="text-4xl font-bold text-blue-600">₦{{ number_format($property->asking_price, 0) }}</p>
                    </div>
                    <div class="mb-6 pb-6 border-b">
                        <p class="text-slate-600 text-sm">{{ $property->status === 'available' ? 'Available' : 'Not Available' }}</p>
                    </div>

                    <!-- Inquiry Form -->
                    <form action="{{ route('properties.inquiry', $property->slug) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="text" name="name" placeholder="Your Name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                        <input type="email" name="email" placeholder="Your Email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                        <input type="tel" name="phone" placeholder="Phone Number" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                        <textarea name="message" placeholder="Your Message" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"></textarea>
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">Send Inquiry</button>
                    </form>

                    @auth
                        <form action="{{ route('properties.favorite', $property->slug) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full border-2 border-blue-600 text-blue-600 py-3 rounded-lg font-semibold hover:bg-blue-50">
                                ♥ Save Property
                            </button>
                        </form>
                    @endauth
                </div>

                <!-- Contact Agent -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4">Contact Agent</h3>
                    <p class="text-slate-600 mb-4">Have questions? Reach out to our team</p>
                    <a href="{{ route('contact') }}" class="block bg-slate-900 text-white py-3 text-center rounded-lg hover:bg-slate-800 mb-2">Contact Now</a>
                    <p class="text-center text-sm text-slate-600">+234 800 000 0000</p>
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <script>
            alert('{{ session("success") }}');
        </script>
    @endif
@endsection
