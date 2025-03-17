<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function getActiveProjects()
    {
        return Project::where('status', 'Berjalan')
            ->get(['code', 'name'])
            ->map(function($project) {
                return [
                    'code' => $project->code,
                    'name' => $project->name
                ];
            });
    }
} 