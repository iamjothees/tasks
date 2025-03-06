<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can act on task timer', function () {
    // Arrange
    $task = Task::factory()->create();
    $user = User::factory()->create();
    $task->assignees()->attach($user->id);
    $this->actingAs($user);
    
    // Act && Assert
    expect(
        Gate::authorize('act-on-task-timer', ['task' => $task])->allowed()
    )->toBeTrue();
});

it('can\'t act on task timer', function () {
    // Arrange
    $task = Task::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Act
    Gate::authorize('act-on-task-timer', ['task' => $task]);
})->throws(AuthorizationException::class);
