@extends('layouts.public')

@section('title', 'Investment Opportunities')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold mb-4 text-center">Investment Opportunities</h1>
        <p class="text-center text-slate-600 mb-12 max-w-2xl mx-auto">
            Join thousands of investors who are building wealth through real estate. Our projects offer transparent terms, regular updates, and proven returns.
        </p>

        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg p-12 mb-16">
            <h2 class="text-3xl font-bold mb-4">Why Invest in Zantex Projects?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <p class="text-4xl font-bold mb-2">15-25%</p>
                    <p>Average Annual Returns</p>
                </div>
                <div>
                    <p class="text-4xl font-bold mb-2">50+</p>
                    <p>Completed Projects</p>
                </div>
                <div>
                    <p class="text-4xl font-bold mb-2">5000+</p>
                    <p>Satisfied Investors</p>
                </div>
            </div>
        </div>

        <!-- Investment Options -->
        <h2 class="text-2xl font-bold mb-8">Investment Options</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-blue-600">
                <h3 class="text-2xl font-bold mb-4">Equity Investment</h3>
                <p class="text-slate-700 mb-6">
                    Own a share of our premium properties. Earn monthly rental income and benefit from property appreciation over time.
                </p>
                <ul class="space-y-2 text-slate-700 mb-6">
                    <li>✓ Minimum Investment: ₦500,000</li>
                    <li>✓ Monthly Returns: 1.5-2.5%</li>
                    <li>✓ Investment Period: Flexible</li>
                    <li>✓ Liquid: Can be sold anytime</li>
                </ul>
                <a href="{{ route('contact') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Learn More</a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-green-600">
                <h3 class="text-2xl font-bold mb-4">Project Investment</h3>
                <p class="text-slate-700 mb-6">
                    Invest in our development projects from inception. Higher returns with defined project completion timeline.
                </p>
                <ul class="space-y-2 text-slate-700 mb-6">
                    <li>✓ Minimum Investment: ₦1,000,000</li>
                    <li>✓ Project Returns: 20-30%</li>
                    <li>✓ Investment Period: 2-4 years</li>
                    <li>✓ Fixed Allocation: Units allocated upfront</li>
                </ul>
                <a href="{{ route('contact') }}" class="inline-block bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Explore Projects</a>
            </div>
        </div>

        <!-- How it Works -->
        <div class="bg-slate-50 rounded-lg p-12">
            <h2 class="text-2xl font-bold mb-8 text-center">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl">1</div>
                    <h3 class="font-bold mb-2">Browse</h3>
                    <p class="text-slate-600 text-sm">View our investment opportunities and select projects that match your goals.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl">2</div>
                    <h3 class="font-bold mb-2">Invest</h3>
                    <p class="text-slate-600 text-sm">Make your investment securely through our platform with transparent terms.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl">3</div>
                    <h3 class="font-bold mb-2">Earn</h3>
                    <p class="text-slate-600 text-sm">Receive regular updates and earn consistent returns from your investment.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl">4</div>
                    <h3 class="font-bold mb-2">Grow</h3>
                    <p class="text-slate-600 text-sm">Watch your investment grow as projects are completed and property values appreciate.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
