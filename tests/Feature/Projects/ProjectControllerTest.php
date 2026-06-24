<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_non_connecte_ne_peut_pas_acceder_aux_projets(): void
    {
        $this->fromFrontend()->getJson('/api/projects')->assertUnauthorized();
    }

    public function test_un_utilisateur_ne_voit_que_ses_propres_projets(): void
    {
        $user = User::factory()->create();
        $autreUtilisateur = User::factory()->create();

        Project::factory()->for($user)->count(2)->create();
        Project::factory()->for($autreUtilisateur)->count(3)->create();

        $response = $this->fromFrontend()->actingAs($user)->getJson('/api/projects');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_un_utilisateur_peut_creer_un_projet(): void
    {
        $user = User::factory()->create();

        $response = $this->fromFrontend()->actingAs($user)->postJson('/api/projects', [
            'name' => 'Refonte du site web',
            'description' => 'Migration vers Laravel + Nuxt',
            'due_date' => now()->addDays(10)->toDateString(),
        ]);

        $response->assertCreated();
        $response->assertJsonPath('name', 'Refonte du site web');
        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'name' => 'Refonte du site web',
        ]);
    }

    public function test_la_creation_echoue_sans_nom(): void
    {
        $user = User::factory()->create();

        $response = $this->fromFrontend()->actingAs($user)->postJson('/api/projects', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_la_creation_echoue_si_la_date_limite_est_dans_le_passe(): void
    {
        $user = User::factory()->create();

        $response = $this->fromFrontend()->actingAs($user)->postJson('/api/projects', [
            'name' => 'Projet test',
            'due_date' => now()->subDay()->toDateString(),
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['due_date']);
    }

    public function test_un_utilisateur_peut_consulter_un_de_ses_projets(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->fromFrontend()->actingAs($user)->getJson("/api/projects/{$project->id}");

        $response->assertOk();
        $response->assertJsonPath('id', $project->id);
    }

    public function test_un_utilisateur_ne_peut_pas_consulter_le_projet_dun_autre(): void
    {
        $proprietaire = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $project = Project::factory()->for($proprietaire)->create();

        $response = $this->fromFrontend()->actingAs($autreUtilisateur)->getJson("/api/projects/{$project->id}");

        $response->assertForbidden();
    }

    public function test_un_utilisateur_peut_modifier_un_de_ses_projets(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create(['name' => 'Ancien nom']);

        $response = $this->fromFrontend()->actingAs($user)->putJson("/api/projects/{$project->id}", [
            'name' => 'Nouveau nom',
        ]);

        $response->assertOk();
        $response->assertJsonPath('name', 'Nouveau nom');
    }

    public function test_un_utilisateur_ne_peut_pas_modifier_le_projet_dun_autre(): void
    {
        $proprietaire = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $project = Project::factory()->for($proprietaire)->create();

        $response = $this->fromFrontend()->actingAs($autreUtilisateur)->putJson("/api/projects/{$project->id}", [
            'name' => 'Tentative de modification',
        ]);

        $response->assertForbidden();
    }

    public function test_modifier_la_date_limite_reinitialise_le_rappel_deja_envoye(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->dueInDays(3)->reminderAlreadySent()->create();

        $this->fromFrontend()->actingAs($user)->putJson("/api/projects/{$project->id}", [
            'due_date' => now()->addDays(10)->toDateString(),
        ]);

        $this->assertNull($project->refresh()->reminder_sent_at);
    }

    public function test_un_utilisateur_peut_supprimer_un_de_ses_projets(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->fromFrontend()->actingAs($user)->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_un_utilisateur_ne_peut_pas_supprimer_le_projet_dun_autre(): void
    {
        $proprietaire = User::factory()->create();
        $autreUtilisateur = User::factory()->create();
        $project = Project::factory()->for($proprietaire)->create();

        $response = $this->fromFrontend()->actingAs($autreUtilisateur)->deleteJson("/api/projects/{$project->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }
}