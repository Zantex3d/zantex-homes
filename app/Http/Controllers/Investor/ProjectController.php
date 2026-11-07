<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentProject;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $projects = DevelopmentProject::whereHas('investors', function ($q) use ($user) {
            $q->where('investor_id', $user->id);
        })->paginate(10);

        return view('investor.projects.index', compact('projects'));
    }

    public function show($projectId): View
    {
        $user = auth()->user();
        $project = DevelopmentProject::where('id', $projectId)
            ->whereHas('investors', function ($q) use ($user) {
                $q->where('investor_id', $user->id);
            })
            ->firstOrFail();

        return view('investor.projects.show', compact('project'));
    }
}
