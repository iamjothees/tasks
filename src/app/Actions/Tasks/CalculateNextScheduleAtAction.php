<?php

namespace App\Actions\Tasks;

use App\Enums\TaskRecursion;
use App\Models\Task;
use Illuminate\Support\Carbon;

class CalculateNextScheduleAtAction
{
    /**
     * Create a new class instance.
     */

    public function __construct( protected Task $task )
    {
        //
    }

    public function execute(): ?Carbon
    {
        if ( in_array($this->task->recursion, [TaskRecursion::NONE, TaskRecursion::NEVER, TaskRecursion::CUSTOM]) ) return null;

        return match($this->task->recursion){
            TaskRecursion::DAILY => $this->task->next_schedule_at->addDay(),
            TaskRecursion::WEEKLY => $this->task->next_schedule_at->addWeek(),
            TaskRecursion::MONTHLY => $this->task->next_schedule_at->addMonthNoOverflow(),
            TaskRecursion::YEARLY => $this->task->next_schedule_at->addYearNoOverflow(),
        };
        
    }
}
