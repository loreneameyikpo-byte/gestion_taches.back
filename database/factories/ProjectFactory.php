<?php

namespace database\factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'due_date' => null,
            'user_id' => User::factory(), // Associe un utilisateur aléatoire à chaque projet
            'reminder' => null, // 50% de chances d'avoir un rappel
        ];
    }

    public function dueInDays(int $days = 3): static
    {
        return $this->state(fn () =>  [
                'due_date' => now()->addDays($days)->toDateString(),
            ]);
    }

    public function reminderAlreadySent(): static
    {
        return $this->state(fn () =>  [
                'reminder' => now()->addDays(1)->toDateString(),
            ]);
    }

}