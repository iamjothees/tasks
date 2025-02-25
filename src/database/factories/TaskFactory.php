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
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'next_schedule_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            'recursion' => $this->faker->randomElement(TaskRecursion::cases()),
        ];
    }

    public function completed($at = null): self{
        return $this->state(fn () => [ 'completed_at' => $at ?? $this->faker->dateTimeBetween(), ]);
    }
}
