<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ZANTEX HOMES</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-slate-900">
    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-blue-600">
                <a href="{{ route('home') }}">ZANTEX HOMES</a>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
                <a href="{{ route('properties.search') }}" class="hover:text-blue-600">Properties</a>
                <a href="{{ route('land.index') }}" class="hover:text-blue-600">Land</a>
                <a href="{{ route('shortlets.index') }}" class="hover:text-blue-600">Shortlets</a>
                <a href="{{ route('projects.index') }}" class="hover:text-blue-600">Projects</a>
                <a href="{{ route('services') }}" class="hover:text-blue-600">Services</a>
                <a href="{{ route('investments') }}" class="hover:text-blue-600">Invest</a>
                <a href="{{ route('contact') }}" class="hover:text-blue-600">Contact</a>
            </div>
            <div class="flex space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button class="px-4 py-2 text-slate-600 hover:text-slate-900">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-blue-600 hover:text-blue-700">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-blue-400">Home</a></li>
                        <li><a href="{{ route('properties.search') }}" class="hover:text-blue-400">Properties</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-blue-400">About</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-blue-400">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Contact</h3>
                    <p class="text-sm">Email: info@zantex.com<br>Phone: +234 800 000 0000<br>Lagos, Nigeria</p>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Follow Us</h3>
                    <div class="space-y-2 text-sm">
                        <a href="#" class="block hover:text-blue-400">Facebook</a>
                        <a href="#" class="block hover:text-blue-400">Instagram</a>
                        <a href="#" class="block hover:text-blue-400">Twitter</a>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Newsletter</h3>
                    <input type="email" placeholder="Your email" class="w-full px-3 py-2 rounded text-slate-900 mb-2">
                    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Subscribe</button>
                </div>
            </div>
            <div class="border-t border-slate-700 mt-8 pt-8 text-center text-sm text-slate-400">
                <p>&copy; 2024 Zantex Homes. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
