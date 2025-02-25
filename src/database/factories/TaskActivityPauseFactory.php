<?php

namespace Database\Factories;

use App\Models\TaskActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskActivityPause>
 */
class TaskActivityPauseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_activity_id' => TaskActivity::factory(),
            'paused_at' => fn ($attributes) => $this->faker->dateTimeBetween(TaskActivity::find($attributes['task_activity_id'])->started_at, TaskActivity::find($attributes['task_activity_id'])->completed_at ?? now()),
        ];
    }

    public function resumed( $at = null){
        return $this->state(function () use ($at) {
            return [
                'resumed_at' => fn ($attributes) => $at ?? $this->faker->dateTimeBetween($attributes['paused_at'], now()),
            ];
        });
    }
}
