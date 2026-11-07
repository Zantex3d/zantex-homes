@extends('layouts.public')

@section('title', 'Services')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold mb-4 text-center">Our Services</h1>
        <p class="text-center text-slate-600 mb-12 max-w-2xl mx-auto">
            We offer comprehensive real estate solutions tailored to meet your needs, whether you're looking to buy, sell, invest, or rent.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Property Sales -->
            <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-lg transition">
                <div class="text-4xl mb-4">🏠</div>
                <h3 class="text-2xl font-bold mb-4">Property Sales</h3>
                <p class="text-slate-700 leading-relaxed mb-6">
                    Whether you're buying your first home or expanding your portfolio, we have verified properties across Lagos with flexible payment plans.
                </p>
                <a href="{{ route('properties.search') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Browse Properties →</a>
            </div>

            <!-- Property Management -->
            <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-lg transition">
                <div class="text-4xl mb-4">📋</div>
                <h3 class="text-2xl font-bold mb-4">Property Management</h3>
                <p class="text-slate-700 leading-relaxed mb-6">
                    Our professional team handles all aspects of your property - from tenant screening to maintenance and rent collection.
                </p>
                <a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Contact Us →</a>
            </div>

            <!-- Investment Opportunities -->
            <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-lg transition">
                <div class="text-4xl mb-4">📈</div>
                <h3 class="text-2xl font-bold mb-4">Investment Opportunities</h3>
                <p class="text-slate-700 leading-relaxed mb-6">
                    Invest in high-growth real estate projects with proven returns. We provide transparency, regular updates, and professional project management.
                </p>
                <a href="{{ route('investments') }}" class="text-blue-600 hover:text-blue-700 font-semibold">View Opportunities →</a>
            </div>

            <!-- Shortlet Properties -->
            <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-lg transition">
                <div class="text-4xl mb-4">🛏️</div>
                <h3 class="text-2xl font-bold mb-4">Shortlet Rentals</h3>
                <p class="text-slate-700 leading-relaxed mb-6">
                    Need temporary accommodation? Browse our selection of fully furnished apartments perfect for short-term stays.
                </p>
                <a href="{{ route('shortlets.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold">View Shortlets →</a>
            </div>

            <!-- Land Sales -->
            <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-lg transition">
                <div class="text-4xl mb-4">🌳</div>
                <h3 class="text-2xl font-bold mb-4">Land & Plots</h3>
                <p class="text-slate-700 leading-relaxed mb-6">
                    Prime land plots in strategic locations, perfect for building your dream home or commercial development.
                </p>
                <a href="{{ route('land.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Explore Land →</a>
            </div>

            <!-- Development Projects -->
            <div class="bg-white rounded-lg shadow-md p-8 hover:shadow-lg transition">
                <div class="text-4xl mb-4">🏢</div>
                <h3 class="text-2xl font-bold mb-4">Development Projects</h3>
                <p class="text-slate-700 leading-relaxed mb-6">
                    From concept to completion, we develop world-class residential and commercial projects with modern amenities.
                </p>
                <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold">View Projects →</a>
            </div>
        </div>
    </section>
@endsection
