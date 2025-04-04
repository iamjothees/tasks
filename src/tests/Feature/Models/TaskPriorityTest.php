<?php

use App\Models\TaskPriority;

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