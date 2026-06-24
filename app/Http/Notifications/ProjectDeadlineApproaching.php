<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeadlineApproaching extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->project->due_date->translatedFormat('d/m/Y');
        $projectUrl = rtrim(config('app.frontend_url'), '/').'/projects/'.$this->project->id;

        return (new MailMessage)
            ->subject("Échéance proche : {$this->project->name}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Le projet « {$this->project->name} » arrive à échéance le {$dueDate} (dans 3 jours).")
            ->when($this->project->description, fn (MailMessage $mail) => $mail->line($this->project->description))
            ->action('Voir le projet', $projectUrl)
            ->line("Pensez à vérifier l'avancement des tâches restantes avant cette date.");
    }
}