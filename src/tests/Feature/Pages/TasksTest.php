<?php

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Tests\TestCase;

pest()->uses(TestCase::class);

it('prevents tasks from guest', function () {
    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertStatus(302);
});

it('opens tasks', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertOk();
});

it('lists only assigned tasks', function () {
    // ARRANGE
    $user = User::factory()->create();
    $this->actingAs($user);

    $tasks = Task::factory()->count(10)->create();

    $assignedTasks = $tasks->take(5);
    $assignedTasks->each(function ($task) use ($user) {
        $task->assignees()->attach($user);
    });

    $othersTasks = $tasks->skip(5);


    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertOk()
        ->assertSee($assignedTasks->pluck('title')->toArray())
        ->assertDontSee($othersTasks->pluck('title')->toArray());
});

it('lists only assigned tasks by types', function () {
    // ARRANGE
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $typeTasks = Task::factory()->recycle($type = TaskType::factory()->create([ 'name' => 'Feature' ]))->count(5)->create();
    $randomTypeTasks = Task::factory()->count(5)->create();

    $typeTasks->merge($randomTypeTasks)->each(function ($task) use ($user) {
        $task->assignees()->attach($user);
    });

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index', [$type]))->assertOk()
        ->assertSee($typeTasks->pluck('title')->toArray())
        ->assertDontSee($randomTypeTasks->pluck('title')->toArray());
});

// TODO: Test statuses tab