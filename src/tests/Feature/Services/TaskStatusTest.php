<?php

use App\Models\TaskStatus;
use App\Services\TaskStatusService;
use Tests\TestCase;

pest()->uses(TestCase::class);
it('deletes only statuses without tasks', function (){
    // Arrange
    $status1 = TaskStatus::factory()->hasTasks(3)->create();
    $status2 = TaskStatus::factory()->create();
    $service = app(TaskStatusService::class);

    //Act & Assert
    expect($service->delete(taskStatus: $status1))->toBeFalse();
    expect($service->delete(taskStatus: $status2))->toBeTrue();
});
