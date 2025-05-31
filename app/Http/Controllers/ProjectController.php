<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\CodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(
        private CodeGeneratorService $codeGenerator
    ) {}

    public function index(): JsonResponse
    {
        $projects = auth()->user()->projects()->latest()->get();
        return response()->json($projects);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'elements' => 'array',
            'settings' => 'array'
        ]);

        $validated['user_id'] = auth()->id();
        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);
        return response()->json($project);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'elements' => 'array',
            'settings' => 'array'
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);
        $project->delete();

        return response()->json(null, 204);
    }

    public function export(Request $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $format = $request->input('format', 'html');
        $content = $this->codeGenerator->generate($project, $format);

        return response()->json([
            'content' => $content,
            'filename' => $project->name . '-' . now()->format('Y-m-d-H-i-s')
        ]);
    }

    public function preview(Project $project)
    {
        $html = $this->codeGenerator->generate($project, 'html');
        return response($html)->header('Content-Type', 'text/html');
    }
}
