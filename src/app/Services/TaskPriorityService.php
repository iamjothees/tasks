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
}
