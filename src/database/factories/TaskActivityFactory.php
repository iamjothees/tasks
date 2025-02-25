<?php

namespace Database\Factories;

use App\Models\TaskAssignee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskActivity>
 */
class TaskActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'started_at' => $this->faker->dateTimeBetween('-1 week', now()),
        ];
    }

    public function completed($at = null): self{
        return $this->state(fn () => [ 
            'completed_at' => fn ($attributes) => $at ?? $this->faker->dateTimeBetween($attributes['started_at'], now()), 
        ] );
    }
}
