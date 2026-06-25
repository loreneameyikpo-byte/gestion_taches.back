<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Les tâches de ce projet.
     *
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

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
        static::saving(function (Project $project) {
            if ($project->isDirty('due_date')) {
                $project->reminder_sent_at = null;
            }
        });
    }
}