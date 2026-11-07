@extends('layouts.public')

@section('title', 'Development Projects')

@section('content')
    <section class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold mb-8">Development Projects</h1>

        @if ($projects->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($projects as $project)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="h-64 bg-slate-200">
                            @if ($project->pictures()->exists())
                                <img src="{{ Storage::url($project->pictures()->first()->path) }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-bold text-2xl">{{ $project->name }}</h3>
                                    <p class="text-slate-600">{{ $project->location?->name }}</p>
                                </div>
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">{{ ucfirst($project->status) }}</span>
                            </div>

                            <div class="mb-4 pb-4 border-b">
                                <p class="text-slate-600 text-sm mb-2">Completion Progress</p>
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->completion_percentage ?? 0 }}%"></div>
                                </div>
                                <p class="text-sm mt-2">{{ $project->completion_percentage ?? 0 }}% Complete</p>
                            </div>

                            <p class="text-slate-700 mb-6">{{ Str::limit($project->description, 150) }}</p>

                            <a href="{{ route('projects.show', $project->slug) }}" class="block text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                                View Project Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $projects->links() }}
            </div>
        @else
            <div class="bg-slate-50 rounded-lg p-12 text-center">
                <p class="text-slate-600 text-lg">No projects available at this time.</p>
            </div>
        @endif
    </section>
@endsection
