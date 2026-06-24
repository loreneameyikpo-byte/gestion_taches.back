<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Notifications\ProjectDeadlineApproaching;
use Illuminate\Console\Command;

class SendProjectDeadlineReminders extends Command
{
    protected $signature = 'reminders:send-project-deadlines';

    protected $description = 'Envoie un email aux propriétaires des projets dont la date limite est dans 3 jours';

    public function handle(): int
    {
        $targetDate = now()->addDays(3)->toDateString();

        $projects = Project::query()
            ->whereDate('due_date', $targetDate)
            ->whereNull('reminder_sent_at')
            ->with('user')
            ->get();

        foreach ($projects as $project) {
            $project->user->notify(new ProjectDeadlineApproaching($project));

            // updateQuietly : on ne veut pas redéclencher l'événement
            // "saving" du modèle (qui réinitialiserait reminder_sent_at
            // si on changeait due_date, ce qui n'est pas le cas ici).
            $project->updateQuietly(['reminder_sent_at' => now()]);

            $this->line("Rappel envoyé pour le projet #{$project->id} ({$project->name}) à {$project->user->email}");
        }

        $this->info("{$projects->count()} rappel(s) envoyé(s) pour le ".now()->addDays(3)->translatedFormat('d/m/Y').'.');

        return self::SUCCESS;
    }
}