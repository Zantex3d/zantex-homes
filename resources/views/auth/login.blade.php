@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">ZANTEX HOMES</h1>
            <p class="text-slate-600 dark:text-slate-400">Real Estate Operating System</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6 bg-white dark:bg-slate-800 p-8 rounded-lg shadow">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">Email Address</label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    autocomplete="email"
                    required
                    value="{{ old('email') }}"
                    class="mt-1 w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"
                >
                @error('email')
                    <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    autocomplete="current-password"
                    required
                    class="mt-1 w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"
                >
            </div>

            <button 
                type="submit" 
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
            >
                Sign In
            </button>

            <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                    Sign up
                </a>
            </p>
        </form>
    </div>
</div>
@endsection
