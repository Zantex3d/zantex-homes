@extends('layouts.public')

@section('title', 'Contact Us')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold mb-8 text-center">Contact Us</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            <!-- Contact Info -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h3 class="text-xl font-bold mb-6">Get in Touch</h3>
                    
                    <div class="mb-8">
                        <h4 class="font-bold mb-2">📍 Address</h4>
                        <p class="text-slate-600">
                            123 Lekki Phase 1<br>
                            Lagos, Nigeria
                        </p>
                    </div>

                    <div class="mb-8">
                        <h4 class="font-bold mb-2">📞 Phone</h4>
                        <p class="text-slate-600">
                            +234 800 000 0000<br>
                            +234 1 234 5678
                        </p>
                    </div>

                    <div class="mb-8">
                        <h4 class="font-bold mb-2">✉️ Email</h4>
                        <p class="text-slate-600">
                            info@zantex.com<br>
                            support@zantex.com
                        </p>
                    </div>

                    <div class="mb-8">
                        <h4 class="font-bold mb-2">🕐 Hours</h4>
                        <p class="text-slate-600">
                            Monday - Friday: 9am - 6pm<br>
                            Saturday: 10am - 4pm<br>
                            Sunday: Closed
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h3 class="text-xl font-bold mb-6">Send us a Message</h3>

                    @if (session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-600 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-600 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Subject</label>
                            <input type="text" name="subject" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-600 @error('subject') border-red-500 @enderror">
                            @error('subject')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                            <textarea name="message" rows="6" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-600 @error('message') border-red-500 @enderror"></textarea>
                            @error('message')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Placeholder -->
        <div class="bg-slate-200 rounded-lg h-96 flex items-center justify-center">
            <p class="text-slate-600">Map location would be displayed here</p>
        </div>
    </section>
@endsection
