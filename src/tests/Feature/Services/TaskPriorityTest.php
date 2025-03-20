<?php

use App\Models\TaskPriority;
use App\Services\TaskPriorityService;
use Tests\TestCase;

pest()->uses(TestCase::class);
it('deletes only priorities without tasks', function (){
    // Arrange
    $priority1 = TaskPriority::factory()->hasTasks(3)->create();
    $priority2 = TaskPriority::factory()->create();
    $service = app(TaskPriorityService::class);

    //Act & Assert
    expect($service->delete(taskPriority: $priority1))->toBeFalse();
    expect($service->delete(taskPriority: $priority2))->toBeTrue();
});
