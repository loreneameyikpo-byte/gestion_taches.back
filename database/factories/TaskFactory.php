<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'priority' => $this->faker->randomElement(TaskPriority::cases()),
            'status' => TaskStatus::Todo,
            'due_date' => null,
            'position' => 0,
        ];
    }

    public function withStatus(TaskStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }

    public function withPriority(TaskPriority $priority): static
    {
        return $this->state(fn () => ['priority' => $priority]);
    }
}