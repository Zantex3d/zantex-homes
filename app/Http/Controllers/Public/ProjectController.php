<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentProject;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = DevelopmentProject::with(['pictures', 'location'])
            ->paginate(12);

        return view('public.projects.index', compact('projects'));
    }

    public function show($slug): View
    {
        $project = DevelopmentProject::with(['pictures', 'location'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.projects.show', compact('project'));
    }
}
