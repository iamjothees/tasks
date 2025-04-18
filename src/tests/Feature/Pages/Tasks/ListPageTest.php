<?php

use App\Filament\Resources\TaskResource;
use App\Filament\Resources\TaskResource\Pages\ListTasks;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Illuminate\Support\Facades\Auth;

use function Pest\Livewire\livewire;

pest()->group('tasks list page');
it('opens tasks', function () {
    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertOk();
});

it('prevents tasks from guest', function () {
    // ARRANGE
    Auth::logout();
    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertStatus(302);
});

it('lists tasks by assignee', function () {
    // ARRANGE
    $tasks = Task::factory()->count(10)->create();

    $assignedTasks = $tasks->take(5);
    $assignedTasks->each(function ($task) {
        $task->assignees()->attach(Auth::user());
    });

    $othersTasks = $tasks->skip(5);


    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index'))->assertOk()
        ->assertSee($assignedTasks->pluck('title')->toArray())
        ->assertDontSee($othersTasks->pluck('title')->toArray());
});

it('lists tasks by types', function () {
    // ARRANGE
        
    $typeTasks = Task::factory()->recycle($type = TaskType::factory()->create([ 'name' => 'Feature' ]))->count(5)->create();
    $randomTypeTasks = Task::factory()->count(5)->create();

    $typeTasks->merge($randomTypeTasks)->each(function ($task) {
        $task->assignees()->attach(Auth::user());
    });

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index', [$type]))
        ->assertOk()
        ->assertSee($typeTasks->pluck('title')->toArray())
        ->assertDontSee($randomTypeTasks->pluck('title')->toArray());
});

it('lists tasks by statuses', function () {
    // ARRANGE
        
    $statusTasks = Task::factory()->recycle($status = TaskStatus::factory()->create([ 'name' => 'Urgent' ]))->count(5)->create();
    $randomStatusTasks = Task::factory()->count(5)->create();

    $statusTasks->merge($randomStatusTasks)->each(function ($task) {
        $task->assignees()->attach(Auth::user());
    });

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index', ['activeTab' => $status->name]))
        ->assertOk()
        ->assertSee($statusTasks->pluck('title')->toArray())
        ->assertDontSee($randomStatusTasks->pluck('title')->toArray());
});

it('lists tasks by type and statuses', function () {
    // ARRANGE
        
    $typeAndStatusTasks = Task::factory()
        ->recycle($type = TaskType::factory()->create([ 'name' => 'Feature' ]))
        ->recycle($status = TaskStatus::factory()->create([ 'name' => 'Urgent' ]))
        ->count(5)->create();

    $typeTasks = Task::factory()->recycle($type)->count(2)->create();
    $statusTasks = Task::factory()->recycle($status)->count(2)->create();

    $typeAndStatusTasks->merge($typeTasks)->merge($statusTasks)->each(function ($task) {
        $task->assignees()->attach(Auth::user());
    });

    // ACT & ASSERT
    $this->get(TaskResource::getUrl('index', ['type' => $type, 'activeTab' => $status->name]))
        ->assertOk()
        ->assertSee($typeAndStatusTasks->pluck('title')->toArray())
        ->assertDontSee($typeTasks->merge($statusTasks)->pluck('title')->toArray());
});

it('performs page actions', function () {
    livewire(ListTasks::class)
        ->mountAction(CreateAction::class)
        ->assertSee('Create Task');
});

it('performs table actions', function () {
    // ARRANGE
    $task = Task::factory()->create();
    $task->assignees()->attach(Auth::user());
    
    // ACT & ASSERT
    livewire(ListTasks::class)

        ->mountTableAction('edit-slideover', $task)
        ->assertSee('Edit Task')
        ->unmountTableAction()
        ->assertDontSee('Edit Task')

        ->mountTableAction('view-slideover', $task)
        ->assertSee('View Task')
        ->unmountTableAction()
        ->assertDontSee('View Task')
        
        ->assertTableActionHasUrl(ViewAction::class, ViewTask::getUrl([$task]), $task);
});