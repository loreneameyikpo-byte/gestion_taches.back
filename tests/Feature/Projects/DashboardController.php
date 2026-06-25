<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $totalProjects = $request->user()->projects()->count();

        $tasksQuery = Task::query()->whereHas(
            'project',
            fn ($query) => $query->where('user_id', $userId)
        );

        $totalTasks = $tasksQuery->count();

        $tasksByStatus = (clone $tasksQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $tasksByPriority = (clone $tasksQuery)
            ->selectRaw('priority, count(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        return response()->json([
            'total_projects' => $totalProjects,
            'total_tasks' => $totalTasks,
            'completed_tasks' => (int) ($tasksByStatus[TaskStatus::Done->value] ?? 0),
            'in_progress_tasks' => (int) ($tasksByStatus[TaskStatus::InProgress->value] ?? 0),
            'todo_tasks' => (int) ($tasksByStatus[TaskStatus::Todo->value] ?? 0),
            'tasks_by_priority' => [
                'low' => (int) ($tasksByPriority[TaskPriority::Low->value] ?? 0),
                'medium' => (int) ($tasksByPriority[TaskPriority::Medium->value] ?? 0),
                'high' => (int) ($tasksByPriority[TaskPriority::High->value] ?? 0),
            ],
        ]);
    }
}