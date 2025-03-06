<?php

namespace App\Services;

use App\Models\TaskActivity;
use App\Models\User;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getActiveTaskActivities(User $user){
        return TaskActivity::query()
            ->whereHas('taskAssignee', fn ($q) => $q->where('assignee_id', $user->id) )
            ->whereNull('completed_at')
            ->get();
    }
}
