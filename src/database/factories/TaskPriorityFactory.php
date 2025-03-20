<?php

namespace Database\Factories;

use App\Models\TaskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskPriority>
 */
class TaskPriorityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'level' => fake()->unique()->numberBetween(TaskPriority::$minLevel, TaskPriority::$maxLevel),
            'color' => fake()->hexColor(),
        ];
    }
}
