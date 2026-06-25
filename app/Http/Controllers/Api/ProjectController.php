<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * GET /api/projects
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $request->user()
            ->projects()
            ->withCount('tasks')
            ->latest()
            ->paginate(15);

        return ProjectResource::collection($projects);
    }

    /**
     * POST /api/projects
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $request->user()->projects()->create($request->validated());

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/projects/{project}
     */
    public function show(Project $project): ProjectResource
    {
        Gate::authorize('view', $project);

        return new ProjectResource($project->loadCount('tasks'));
    }

    /**
     * PUT /api/projects/{project}
     */
    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());

        return new ProjectResource($project->refresh());
    }

    /**
     * DELETE /api/projects/{project}
     *
     * Règle métier du cahier des charges : suppression interdite si le
     * projet contient encore des tâches.
     */
    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        if ($project->tasks()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer un projet qui contient encore des tâches.',
            ], 409);
        }

        $project->delete();

        return response()->json(null, 204);
    }
}