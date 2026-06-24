<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'description', 'due_date'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * Le propriétaire du projet.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // La relation tasks() (hasMany) sera ajoutée à l'Étape 4
    // "Gestion des tâches" lorsque le modèle Task existera.

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'reminder_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Si la date limite change, on remet le compteur de rappel à zéro :
        // sans ça, repousser une échéance déjà notifiée empêcherait tout
        // nouveau rappel d'être envoyé pour la nouvelle date.
        static::saving(function (Project $project) {
            if ($project->isDirty('due_date')) {
                $project->reminder_sent_at = null;
            }
        });
    }
}