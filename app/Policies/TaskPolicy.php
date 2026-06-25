<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Une tâche n'a pas de user_id direct : l'autorisation passe par le
     * propriétaire du projet auquel elle appartient.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->project->user_id;
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->project->user_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->project->user_id;
    }
}