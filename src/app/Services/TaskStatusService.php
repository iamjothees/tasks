<?php

namespace App\Services;

use App\Models\TaskStatus;

class TaskStatusService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function store(array $data): TaskStatus{
        return TaskStatus::create($data);
    }

    public function update(TaskStatus $taskStatus, array $data): TaskStatus{
        $taskStatus->update($data);
        return $taskStatus;
    }

    public function delete(TaskStatus $taskStatus): bool{
        if ( $taskStatus->tasks()->exists() ) return false;
        return $taskStatus->delete();
    }
}
