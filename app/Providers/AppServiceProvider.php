<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Policies\ProjectPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;

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
 
        // Par défaut, Laravel enveloppe toute ressource API simple dans
        // {"data": {...}} (ex: UserResource -> {"data": {"id":1,...}}).
        // On désactive ce comportement pour renvoyer l'objet directement
        // ({"id":1,...}), plus simple à consommer côté frontend. Les
        // collections PAGINÉES (ProjectResource::collection(...)) ne sont
        // pas concernées : Laravel force toujours leur enveloppe
        // data/links/meta, indépendamment de ce réglage.
        JsonResource::withoutWrapping();
    }
}
