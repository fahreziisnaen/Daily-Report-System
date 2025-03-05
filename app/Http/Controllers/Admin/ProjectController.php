<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        return view('admin.projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:projects,code',
            'name' => 'required|string',
            'customer' => 'required|string',
            'status' => 'required|in:Berjalan,Selesai',
        ]);

        Project::create($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project berhasil dibuat.');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:projects,code,' . $project->id,
            'name' => 'required|string',
            'customer' => 'required|string',
            'status' => 'required|in:Berjalan,Selesai',
        ]);

        $project->update($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        if ($project->reports()->exists()) {
            return back()->with('error', 'Project tidak dapat dihapus karena masih memiliki laporan terkait.');
        }

        $project->delete();
        return back()->with('success', 'Project berhasil dihapus.');
    }

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