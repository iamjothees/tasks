<?php

use App\Models\TaskPriority;
use Tests\TestCase;

pest()->uses(TestCase::class);

describe('relationship', function (){
    it('tasks', function (){
        //Arrange
        $priority = TaskPriority::factory()
            ->hasTasks(2)
            ->create();

        //Assert
        expect($priority->tasks)->toHaveCount(2);
    });
});