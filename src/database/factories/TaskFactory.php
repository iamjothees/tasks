<?php

namespace Database\Factories;

use App\Enums\TaskRecursion;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->sentence(),
            'type_id' => TaskType::factory(),

            'priority_level' => fn () => TaskPriority::factory(),
            'status_level' => fn () => TaskStatus::factory(),

            'next_schedule_at' => fake()->dateTimeBetween('now', '+1 week'),
            'recursion' => fake()->randomElement(TaskRecursion::cases()),
        ];
    }

    public function completed($at = null): self{
        return $this->state(fn () => [ 'completed_at' => $at ?? fake()->dateTimeBetween(), ]);
    }
}
