<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_lister_les_taches_de_son_projet(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        Task::factory()->for($project)->count(3)->create();

        $response = $this->fromFrontend()->actingAs($user)->getJson("/api/projects/{$project->id}/tasks");

        $response->assertOk();
        $response->assertJsonCount(3);
    }

    public function test_un_utilisateur_ne_peut_pas_lister_les_taches_du_projet_dun_autre(): void
    {
        $proprietaire = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $project = Project::factory()->for($proprietaire)->create();

        $response = $this->fromFrontend()->actingAs($autreUtilisateur)->getJson("/api/projects/{$project->id}/tasks");

        $response->assertForbidden();
    }

    public function test_un_utilisateur_peut_creer_une_tache_dans_son_projet(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->fromFrontend()->actingAs($user)->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'Rédiger le cahier des charges',
            'priority' => 'high',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('title', 'Rédiger le cahier des charges');
        $response->assertJsonPath('priority', 'high');
        $response->assertJsonPath('status', 'todo');
        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Rédiger le cahier des charges',
        ]);
    }

    public function test_la_creation_echoue_sans_titre(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->fromFrontend()->actingAs($user)->postJson("/api/projects/{$project->id}/tasks", []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_la_creation_echoue_avec_une_priorite_invalide(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->fromFrontend()->actingAs($user)->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'Tâche test',
            'priority' => 'extreme',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['priority']);
    }

    public function test_un_utilisateur_ne_peut_pas_creer_de_tache_dans_le_projet_dun_autre(): void
    {
        $proprietaire = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $project = Project::factory()->for($proprietaire)->create();

        $response = $this->fromFrontend()->actingAs($autreUtilisateur)->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'Intrusion',
        ]);

        $response->assertForbidden();
    }

    public function test_un_utilisateur_peut_modifier_une_de_ses_taches(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Ancien titre']);

        $response = $this->fromFrontend()->actingAs($user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Nouveau titre',
        ]);

        $response->assertOk();
        $response->assertJsonPath('title', 'Nouveau titre');
    }

    public function test_un_utilisateur_ne_peut_pas_modifier_la_tache_dun_autre(): void
    {
        $proprietaire = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $project = Project::factory()->for($proprietaire)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->fromFrontend()->actingAs($autreUtilisateur)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Tentative',
        ]);

        $response->assertForbidden();
    }

    public function test_un_utilisateur_peut_changer_le_statut_dune_tache(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->withStatus(TaskStatus::Todo)->create();

        $response = $this->fromFrontend()->actingAs($user)->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'in_progress',
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'in_progress');
    }

    public function test_le_changement_de_statut_echoue_avec_une_valeur_invalide(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->fromFrontend()->actingAs($user)->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'archived',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_un_utilisateur_peut_supprimer_une_de_ses_taches(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->fromFrontend()->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_un_utilisateur_ne_peut_pas_supprimer_la_tache_dun_autre(): void
    {
        $proprietaire = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $project = Project::factory()->for($proprietaire)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->fromFrontend()->actingAs($autreUtilisateur)->deleteJson("/api/tasks/{$task->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_la_priorite_par_defaut_est_moyenne(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->fromFrontend()->actingAs($user)->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'Tâche sans priorité précisée',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('priority', TaskPriority::Medium->value);
    }
}