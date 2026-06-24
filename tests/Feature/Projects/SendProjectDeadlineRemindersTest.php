<?php

namespace Tests\Feature\Reminders;

use App\Models\Project;
use App\Notifications\ProjectDeadlineApproaching;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendProjectDeadlineRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_rappel_est_envoye_pour_un_projet_a_echeance_dans_exactement_3_jours(): void
    {
        Notification::fake();

        $project = Project::factory()->dueInDays(3)->create();

        $this->artisan('reminders:send-project-deadlines')->assertSuccessful();

        Notification::assertSentTo($project->user, ProjectDeadlineApproaching::class);
        $this->assertNotNull($project->refresh()->reminder_sent_at);
    }

    public function test_aucun_rappel_nest_envoye_pour_une_echeance_dans_5_jours(): void
    {
        Notification::fake();

        $project = Project::factory()->dueInDays(5)->create();

        $this->artisan('reminders:send-project-deadlines');

        Notification::assertNotSentTo($project->user, ProjectDeadlineApproaching::class);
        $this->assertNull($project->refresh()->reminder_sent_at);
    }

    public function test_aucun_rappel_nest_envoye_pour_une_echeance_deja_passee(): void
    {
        Notification::fake();

        $project = Project::factory()->create(['due_date' => now()->subDay()]);

        $this->artisan('reminders:send-project-deadlines');

        Notification::assertNotSentTo($project->user, ProjectDeadlineApproaching::class);
    }

    public function test_un_rappel_deja_envoye_nest_jamais_renvoye(): void
    {
        Notification::fake();

        $project = Project::factory()->dueInDays(3)->reminderAlreadySent()->create();

        $this->artisan('reminders:send-project-deadlines');

        Notification::assertNotSentTo($project->user, ProjectDeadlineApproaching::class);
    }

    public function test_un_projet_sans_date_limite_nenvoie_jamais_de_rappel(): void
    {
        Notification::fake();

        $project = Project::factory()->create(['due_date' => null]);

        $this->artisan('reminders:send-project-deadlines');

        Notification::assertNotSentTo($project->user, ProjectDeadlineApproaching::class);
    }
}