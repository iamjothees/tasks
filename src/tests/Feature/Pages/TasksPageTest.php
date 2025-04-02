<?php

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use Tests\TestCase;

pest()->uses(TestCase::class);

it('opens tasks', function () {
    $this->actingAs($this->user);

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertOk();
});

it('prevents tasks from guest', function () {
    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertStatus(302);
});

it('lists tasks only by assignee', function () {
    // ARRANGE
    $this->actingAs($this->user);

    $tasks = Task::factory()->count(10)->create();

    $assignedTasks = $tasks->take(5);
    $assignedTasks->each(function ($task) {
        $task->assignees()->attach($this->user);
    });

    $othersTasks = $tasks->skip(5);


    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertOk()
        ->assertSee($assignedTasks->pluck('title')->toArray())
        ->assertDontSee($othersTasks->pluck('title')->toArray());
});

it('lists tasks only by types', function () {
    // ARRANGE
    $this->actingAs($this->user);
    
    $typeTasks = Task::factory()->recycle($type = TaskType::factory()->create([ 'name' => 'Feature' ]))->count(5)->create();
    $randomTypeTasks = Task::factory()->count(5)->create();

    $typeTasks->merge($randomTypeTasks)->each(function ($task) {
        $task->assignees()->attach($this->user);
    });

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index', [$type]))
        ->assertOk()
        ->assertSee($typeTasks->pluck('title')->toArray())
        ->assertDontSee($randomTypeTasks->pluck('title')->toArray());
});

it('lists tasks only by statuses', function () {
    // ARRANGE
    $this->actingAs($this->user);
    
    $statusTasks = Task::factory()->recycle($status = TaskStatus::factory()->create([ 'name' => 'Urgent' ]))->count(5)->create();
    $randomStatusTasks = Task::factory()->count(5)->create();

    $statusTasks->merge($randomStatusTasks)->each(function ($task) {
        $task->assignees()->attach($this->user);
    });

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index', ['activeTab' => $status->name]))
        ->assertOk()
        ->assertSee($statusTasks->pluck('title')->toArray())
        ->assertDontSee($randomStatusTasks->pluck('title')->toArray());
});

it('lists tasks only by type and statuses', function () {
    // ARRANGE
    $this->actingAs($this->user);
    
    $typeAndStatusTasks = Task::factory()
        ->recycle($type = TaskType::factory()->create([ 'name' => 'Feature' ]))
        ->recycle($status = TaskStatus::factory()->create([ 'name' => 'Urgent' ]))
        ->count(5)->create();

    $typeTasks = Task::factory()->recycle($type)->count(2)->create();
    $statusTasks = Task::factory()->recycle($status)->count(2)->create();

    $typeAndStatusTasks->merge($typeTasks)->merge($statusTasks)->each(function ($task) {
        $task->assignees()->attach($this->user);
    });

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index', ['type' => $type, 'activeTab' => $status->name]))
        ->assertOk()
        ->assertSee($typeAndStatusTasks->pluck('title')->toArray())
        ->assertDontSee($typeTasks->merge($statusTasks)->pluck('title')->toArray());
});