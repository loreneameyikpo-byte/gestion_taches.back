<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrement explicite plutôt que de compter sur la convention
        // de nommage automatique de Laravel : plus clair à l'audit, et
        // garanti de fonctionner même si la convention change un jour.
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);

        JsonResource::withoutWrapping();
    }
}