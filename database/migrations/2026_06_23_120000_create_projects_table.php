<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();

            // Date limite du PROJET (et non de chaque tâche individuelle) —
            // ajoutée spécifiquement pour le système de rappel par mail.
            $table->date('due_date')->nullable();

            // Horodatage du dernier rappel envoyé pour la date limite
            // actuelle. Remis à null si due_date est modifiée (voir
            // Project::booted() dans le modèle), afin qu'un nouveau
            // rappel parte pour la nouvelle échéance.
            $table->timestamp('reminder_sent_at')->nullable();

            $table->timestamps();

            $table->index(['due_date', 'reminder_sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};