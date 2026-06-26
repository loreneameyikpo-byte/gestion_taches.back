<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     *
     * Toutes les tâches de l'utilisateur connecté, tous projets confondus
     * (utilisé par l'onglet "Tâches" du dashboard). Mêmes filtres que
     * l'index imbriqué : search, status, priority.
     */
    public function all(Request $request): AnonymousResourceCollection
    {
        $tasks = Task::query()
            ->whereHas('project', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with('project:id,name')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%')
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->string('status'))
            )
            ->when(
                $request->filled('priority'),
                fn ($query) => $query->where('priority', $request->string('priority'))
            )
            ->orderBy('due_date')
            ->latest()
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * GET /api/projects/{project}/tasks
     *
     * Filtres optionnels en query string :
     * - search   : recherche partielle dans le titre
     * - status   : todo | in_progress | done
     * - priority : low | medium | high
     */
    public function index(Project $project, Request $request): AnonymousResourceCollection
    {
        Gate::authorize('view', $project);

        $tasks = $project->tasks()
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%')
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->string('status'))
            )
            ->when(
                $request->filled('priority'),
                fn ($query) => $query->where('priority', $request->string('priority'))
            )
            ->orderBy('position')
            ->orderBy('created_at')
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * POST /api/projects/{project}/tasks
     */
    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);

        $task = $project->tasks()->create($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/tasks/{task}
     */
    public function show(Task $task): TaskResource
    {
        Gate::authorize('view', $task);

        return new TaskResource($task);
    }

    /**
     * PUT /api/tasks/{task}
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task->refresh());
    }

    /**
     * PATCH /api/tasks/{task}/status
     *
     * Action dédiée et légère pour le Kanban (Étape 6) : ne valide/ne
     * modifie que le statut, sans toucher au reste de la tâche.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): TaskResource
    {
        Gate::authorize('update', $task);

        $task->update(['status' => $request->validated('status')]);

        return new TaskResource($task->refresh());
    }

    /**
     * DELETE /api/tasks/{task}
     */
    public function destroy(Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }
}