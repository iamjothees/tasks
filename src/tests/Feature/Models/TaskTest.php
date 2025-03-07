<?php

use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use Tests\TestCase;

pest()->uses(TestCase::class);
test('priority relationship', function () {
    // Arrange
    $task = Task::factory()->create();

    expect($task->priority)->toBeInstanceOf(TaskPriority::class);
});


test('status relationship', function () {
    // Arrange
    $task = Task::factory()->create();

    expect($task->status)->toBeInstanceOf(TaskStatus::class);
});
