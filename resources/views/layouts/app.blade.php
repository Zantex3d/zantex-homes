<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ZANTEX HOMES</title>
    
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    @if (auth()->check())
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-slate-100 dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-blue-600 dark:text-blue-400">ZANTEX</h1>
                </div>

                <nav class="px-4 space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Dashboard
                    </a>
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Properties
                    </a>
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Tenants
                    </a>
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Accounting
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Topbar -->
                <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 py-4">
                    <div class="flex justify-between items-center">
                        <div class="text-lg font-semibold">
                            {{ auth()->user()->getActiveCompany()?->trading_name ?? 'ZANTEX HOMES' }}
                        </div>
                        <div class="flex items-center gap-4">
                            <button id="theme-toggle" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                <svg class="w-5 h-5 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.828-2.828a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm.707 5.657a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 1.414l-.707.707zM9 17a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1z"></path>
                                </svg>
                            </button>

                            <div class="relative group">
                                <button class="flex items-center gap-2 text-sm font-medium hover:text-slate-600 dark:hover:text-slate-300">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                                    {{ auth()->user()->name }}
                                </button>

                                <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 hidden group-hover:block z-50">
                                    <a href="#" class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">Profile</a>
                                    <a href="#" class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">Settings</a>
                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-200 dark:border-slate-700">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-8">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg">
                            <ul class="text-red-600 dark:text-red-200 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                            <p class="text-green-600 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endif

    <script>
        document.getElementById('theme-toggle')?.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                localStorage.theme = 'dark';
            } else {
                localStorage.theme = 'light';
            }
        });
    </script>

    @livewireScripts
</body>
</html>
