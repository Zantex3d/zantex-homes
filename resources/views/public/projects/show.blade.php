@extends('layouts.public')

@section('title', $project->name)

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <!-- Gallery -->
                <div class="h-96 bg-slate-200 rounded-lg overflow-hidden mb-8">
                    @if ($project->pictures()->exists())
                        <img id="mainImage" src="{{ Storage::url($project->pictures()->first()->path) }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                    @endif
                </div>

                <!-- Project Info -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                    <h1 class="text-3xl font-bold mb-2">{{ $project->name }}</h1>
                    <p class="text-slate-600 mb-6">{{ $project->location?->name }}</p>

                    <div class="grid grid-cols-3 gap-6 mb-8 pb-8 border-b">
                        <div>
                            <p class="text-sm text-slate-600 mb-2">Status</p>
                            <p class="font-bold text-lg">{{ ucfirst($project->status) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 mb-2">Completion</p>
                            <p class="font-bold text-lg">{{ $project->completion_percentage ?? 0 }}%</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 mb-2">Units</p>
                            <p class="font-bold text-lg">{{ $project->total_units ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">About This Project</h2>
                        <p class="text-slate-700 leading-relaxed">{{ $project->description }}</p>
                    </div>

                    <!-- Progress -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Project Progress</h2>
                        <div class="w-full bg-slate-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ $project->completion_percentage ?? 0 }}%"></div>
                        </div>
                        <p class="text-slate-600 mt-2">{{ $project->completion_percentage ?? 0 }}% Complete</p>
                    </div>

                    <!-- Timeline -->
                    @if ($project->start_date && $project->expected_completion_date)
                        <div>
                            <h2 class="text-2xl font-bold mb-4">Timeline</h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-slate-600 mb-2">Start Date</p>
                                    <p class="font-bold">{{ $project->start_date->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-600 mb-2">Expected Completion</p>
                                    <p class="font-bold">{{ $project->expected_completion_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-blue-50 rounded-lg p-6 sticky top-4">
                    <h3 class="text-xl font-bold mb-6">Interested?</h3>
                    <p class="text-slate-700 mb-6">
                        This is an excellent investment opportunity with proven returns and professional management.
                    </p>
                    <a href="{{ route('contact') }}" class="block text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold mb-4">
                        Get More Information
                    </a>
                    <p class="text-center text-sm text-slate-600">or call us at +234 800 000 0000</p>
                </div>
            </div>
        </div>
    </section>
@endsection
