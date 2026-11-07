@extends('layouts.public')

@section('title', 'About Us')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold mb-8">About Zantex Homes</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <div>
                <h2 class="text-2xl font-bold mb-4">Our Mission</h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    At Zantex Homes, we are committed to connecting people with quality real estate opportunities across Nigeria. 
                    With over 50 successful projects and thousands of satisfied clients, we've established ourselves as a trusted 
                    partner in real estate.
                </p>
                <p class="text-slate-700 leading-relaxed">
                    Our mission is to make real estate investment and ownership accessible, transparent, and rewarding for everyone.
                </p>
            </div>
            <div class="bg-blue-50 rounded-lg p-8">
                <h3 class="text-xl font-bold mb-4">Why Choose Us?</h3>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <span class="text-green-600 mr-3 text-xl">✓</span>
                        <span class="text-slate-700">20+ years of industry experience</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-3 text-xl">✓</span>
                        <span class="text-slate-700">Transparent pricing and processes</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-3 text-xl">✓</span>
                        <span class="text-slate-700">Expert team of professionals</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-3 text-xl">✓</span>
                        <span class="text-slate-700">Verified properties and clear titles</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-3 text-xl">✓</span>
                        <span class="text-slate-700">Excellent customer support</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <div class="text-center">
                <p class="text-4xl font-bold text-blue-600 mb-2">500+</p>
                <p class="text-slate-600">Properties Listed</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-bold text-blue-600 mb-2">5000+</p>
                <p class="text-slate-600">Happy Clients</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-bold text-blue-600 mb-2">50+</p>
                <p class="text-slate-600">Completed Projects</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-bold text-blue-600 mb-2">20+</p>
                <p class="text-slate-600">Years Experience</p>
            </div>
        </div>

        <!-- Team -->
        <div>
            <h2 class="text-3xl font-bold mb-8 text-center">Our Team</h2>
            <p class="text-center text-slate-600 mb-12">
                Our dedicated team of real estate professionals is here to help you find the perfect property.
            </p>
        </div>
    </section>
@endsection
