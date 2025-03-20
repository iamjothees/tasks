<?php

namespace App\Services;

use App\Models\TaskPriority;

class TaskPriorityService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function store(array $data): TaskPriority{
        return TaskPriority::create($data);
    }

    public function update(TaskPriority $taskPriority, array $data): TaskPriority{
        $taskPriority->update($data);
        return $taskPriority;
    }

    public function delete(TaskPriority $taskPriority): bool{
        if ( $taskPriority->tasks()->exists() ) return false;
        return $taskPriority->delete();
    }
}
