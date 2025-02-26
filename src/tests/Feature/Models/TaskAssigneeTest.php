<?php

use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskActivityPause;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->uses(TestCase::class, RefreshDatabase::class);

it('active_activity_relation', function () {
    // Arrange
    $taskAssignee = Task::factory()->hasAssignees(User::factory())->create()->assignees()->first()->pivot; 
    $completedActivity = TaskActivity::factory()->completed()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    $activeActivity = TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    // Assert
    expect($taskAssignee->activeActivity->id)->toBe($activeActivity->id);
    expect($taskAssignee->activeActivity->id)->not()->toBe($completedActivity->id);
});

it('can_start_timer', function () {
    // Arrange
    $task = Task::factory()->hasAssignees(User::factory())->create();
    $taskAssignee = $task->assignees()->first()->pivot;
    //Assert
    expect($taskAssignee->canStartTimer())->toBeTrue();

    // Arrange 
    TaskActivity::factory()->completed()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    $taskAssignee->refresh();
    // Assert
    expect($taskAssignee->canStartTimer())->toBeTrue();

    // Arrange
    TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    $taskAssignee->refresh();
    // Assert
    expect($taskAssignee->canStartTimer())->toBeFalse();
});

it('can_pause_timer', function () {
    // Arrange
    $taskAssignee = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $activity = TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    // Assert
    expect($taskAssignee->canPauseTimer(activity: $activity))->toBeTrue();

    // Arrange
    $taskAssignee2 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $activity2 = TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee2->id ]);
    TaskActivityPause::factory()->resumed()->create([ 'task_activity_id' => $activity2->id, ]);
    // Assert
    expect($taskAssignee2->canPauseTimer(activity: $activity2) )->toBeTrue();


    // Arrange
    $taskAssignee3 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    // Assert
    expect($taskAssignee3->canPauseTimer())->toBeFalse();

    // Arrange
    $taskAssignee4 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $activity3 = TaskActivity::factory()->completed()->create([ 'task_assignee_id' => $taskAssignee4->id ]);
    // Assert
    expect($taskAssignee4->canPauseTimer(activity: $activity3))->toBeFalse();

    // Arrange
    $taskAssignee5 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $activity4 = TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee5->id ]);
    TaskActivityPause::factory()->create([ 'task_activity_id' => $activity4->id ]);
    // Assert
    expect($taskAssignee4->canPauseTimer(activity: $activity4))->toBeFalse();


    // Arrange
    $taskAssignee6 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $activity5 = TaskActivity::factory()->completed()->create([ 'task_assignee_id' => $taskAssignee5->id ]);
    TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee5->id ]);
    TaskActivityPause::factory()->create([ 'task_activity_id' => $activity4->id ]);
    // Assert
    expect($taskAssignee6->canPauseTimer(activity: $activity5))->toBeFalse();
});

it('can_resume_timer', function () {
    // Arrange
    $taskAssignee = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $pause = TaskActivityPause::factory()->create([
        'task_activity_id' => TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ])
    ]);
    // Assert
    expect($taskAssignee->canResumeTimer(pause: $pause))->toBeTrue();

    // Arrange
    $taskAssignee2 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    // Assert
    expect($taskAssignee2->canResumeTimer())->toBeFalse();

    // Arrange
    $taskAssignee3 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $pause2 = TaskActivityPause::factory()->resumed()->create([
        'task_activity_id' => TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee3->id ])
    ]);
    TaskActivityPause::factory()->create([
        'task_activity_id' => TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee3->id ])
    ]);
    // Assert
    expect($taskAssignee3->canResumeTimer(pause: $pause2))->toBeFalse();
});

it('can_stop_timer', function () {
    // Arrange
    $taskAssignee = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $activity = TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    // Assert
    expect($taskAssignee->canStopTimer(activity: $activity))->toBeTrue();

    // Arrange
    $taskAssignee2 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    // Assert
    expect($taskAssignee2->canStopTimer())->toBeFalse();


    // Arrange
    $taskAssignee3 = Task::factory()->hasAssignees(User::factory())->create()->assignees->first()->pivot;
    $activity2 = TaskActivity::factory()->completed()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    TaskActivity::factory()->create([ 'task_assignee_id' => $taskAssignee->id ]);
    // Assert
    expect($taskAssignee3->canStopTimer(activity: $activity2))->toBeFalse();
});