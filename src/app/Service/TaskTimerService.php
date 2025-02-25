<?php

namespace App\Service;

use App\Enums\TaskTimerAction;
use App\Models\TaskActivity;
use App\Models\TaskActivityPause;
use App\Models\TaskAssignee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;

class TaskTimerService
{
    protected Carbon $now;
    protected TaskAssignee $taskAssignee;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->now = Context::get('now', now());
    }

    public function act(TaskAssignee $taskAssignee, TaskTimerAction $action, TaskActivity|TaskActivityPause $actionable)
    {
        $this->taskAssignee = $taskAssignee;
        switch($action){
            case TaskTimerAction::START:
                $this->startTimer();
                break;
            case TaskTimerAction::PAUSE:
                $this->pauseTimer();
                break;
            case TaskTimerAction::RESUME:
                $this->resumeTimer();
                break;
            case TaskTimerAction::STOP:
                $this->stopTimer();
                break;
        }
    }

    protected function startTimer(): Carbon{
        if ( !$this->taskAssignee->can_start_timer ) throw new \Exception('You cannot start a timer');

        return $this->taskAssignee->activities()->create(['started_at' => $this->now])->started_at;
    }

    protected function pauseTimer(): Carbon{
        if ( !$this->taskAssignee->can_pause_timer ) throw new \Exception('You cannot pause a timer');

        return $this->taskAssignee->latestActiveActivity()->create(['started_at' => $this->now])->started_at;
    }

    protected function resumeTimer(): Carbon{
        if ( !$this->taskAssignee->can_start_timer ) throw new \Exception('You cannot start a timer');

        return $this->taskAssignee->activities()->create(['started_at' => $this->now])->started_at;
    }

    protected function stopTimer(): Carbon{
        if ( !$this->taskAssignee->can_start_timer ) throw new \Exception('You cannot start a timer');

        return $this->taskAssignee->activities()->create(['started_at' => $this->now])->started_at;
    }
}
