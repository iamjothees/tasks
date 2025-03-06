<?php

namespace Database\Factories;

use App\Enums\TaskRecursion;
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
            'next_schedule_at' => fake()->dateTimeBetween('now', '+1 week'),
            'recursion' => fake()->randomElement(TaskRecursion::cases()),
        ];
    }

    public function completed($at = null): self{
        return $this->state(fn () => [ 'completed_at' => $at ?? fake()->dateTimeBetween(), ]);
    }
}
