<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();

            // Stockées en string + cast vers un enum PHP (App\Enums\TaskPriority
            // / TaskStatus) côté modèle : plus simple à faire évoluer qu'un
            // ENUM natif MySQL (pas besoin de migration pour ajouter une valeur).
            $table->string('priority')->default('medium');
            $table->string('status')->default('todo');

            $table->date('due_date')->nullable();

            // Position dans la colonne Kanban (Étape 6, drag & drop).
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};