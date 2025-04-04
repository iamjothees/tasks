<?php

use App\Models\TaskStatus;

describe('relationship', function (){
    it('tasks', function (){
        //Arrange
        $priority = TaskStatus::factory()
            ->hasTasks(2)
            ->create();

        //Assert
        expect($priority->tasks)->toHaveCount(2);
    });
});