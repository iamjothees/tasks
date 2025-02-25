<?php

use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskActivityPause;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->uses(TestCase::class, RefreshDatabase::class);

it('can_start_timer', function () {
    // Arrange
    $task = Task::factory()->hasAssignees(User::factory())->create();
    $taskAssignee = $task->assignees()->first()->pivot;
    //Assert
    expect($taskAssignee->can_start_timer)->toBeTrue();

    // Arrange 
    TaskActivity::factory()->completed()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    $taskAssignee->refresh();
    // Assert
    expect($taskAssignee->can_start_timer)->toBeTrue();

    // Arrange
    TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    $taskAssignee->refresh();
    // Assert
    expect($taskAssignee->can_start_timer)->toBeFalse();
});

it('can_pause_timer', function () {
    // Arrange
    $taskAssignee = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    // Assert
    expect($taskAssignee->can_pause_timer)->toBeTrue();

    // Arrange
    $taskAssignee2 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    TaskActivityPause::factory()->resumed()->create([
            'task_activity_id' => TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee2->id ])
        ]);
    // Assert
    expect($taskAssignee->can_pause_timer)->toBeTrue();


    // Arrange
    $taskAssignee3 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    // Assert
    expect($taskAssignee3->can_pause_timer)->toBeFalse();

    // Arrange
    $taskAssignee4 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    TaskActivity::factory()->completed()->create([ 'task_assignee_id' => $taskAssignee4->id ]);
    // Assert
    expect($taskAssignee4->can_pause_timer)->toBeFalse();

    // Arrange
    $taskAssignee5 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    TaskActivityPause::factory()->create([
        'task_activity_id' => TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee5->id ])
    ]);
    // Assert
    expect($taskAssignee4->can_pause_timer)->toBeFalse();
});

it('can_resume_timer', function () {
    // Arrange
    $taskAssignee = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    TaskActivityPause::factory()->create([
        'task_activity_id' => TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ])
    ]);
    // Assert
    expect($taskAssignee->can_resume_timer)->toBeTrue();
});

it('can_stop_timer', function () {
    // Arrange
    $taskAssignee = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    // Assert
    expect($taskAssignee->can_stop_timer)->toBeTrue();
});