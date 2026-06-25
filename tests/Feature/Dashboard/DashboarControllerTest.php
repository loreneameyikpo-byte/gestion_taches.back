<?php

namespace Tests\Feature\Dashboard;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_non_connecte_ne_peut_pas_consulter_le_dashboard(): void
    {
        $this->fromFrontend()->getJson('/api/dashboard')->assertUnauthorized();
    }

    public function test_les_statistiques_sont_correctes_et_limitees_a_lutilisateur_connecte(): void
    {
        $user = User::factory()->create();
        $autreUtilisateur = User::factory()->create();

        $projectA = Project::factory()->for($user)->create();
        $projectB = Project::factory()->for($user)->create();
        Task::factory()->for($projectA)->withStatus(TaskStatus::Done)->withPriority(TaskPriority::High)->create();
        Task::factory()->for($projectA)->withStatus(TaskStatus::InProgress)->withPriority(TaskPriority::Medium)->create();
        Task::factory()->for($projectB)->withStatus(TaskStatus::Todo)->withPriority(TaskPriority::Low)->create();
        Task::factory()->for($projectB)->withStatus(TaskStatus::Todo)->withPriority(TaskPriority::Low)->create();

        $autreProjet = Project::factory()->for($autreUtilisateur)->create();
        Task::factory()->for($autreProjet)->withStatus(TaskStatus::Done)->create();

        $response = $this->fromFrontend()->actingAs($user)->getJson('/api/dashboard');

        $response->assertOk();
        $response->assertJson([
            'total_projects' => 2,
            'total_tasks' => 4,
            'completed_tasks' => 1,
            'in_progress_tasks' => 1,
            'todo_tasks' => 2,
            'tasks_by_priority' => [
                'low' => 2,
                'medium' => 1,
                'high' => 1,
            ],
        ]);
    }

    public function test_un_utilisateur_sans_projet_obtient_des_statistiques_a_zero(): void
    {
        $user = User::factory()->create();

        $response = $this->fromFrontend()->actingAs($user)->getJson('/api/dashboard');

        $response->assertOk();
        $response->assertJson([
            'total_projects' => 0,
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'in_progress_tasks' => 0,
            'todo_tasks' => 0,
        ]);
    }
}