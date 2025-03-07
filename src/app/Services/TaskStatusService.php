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
}
