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

    public function act(TaskAssignee $taskAssignee, TaskTimerAction $action, null|TaskActivity|TaskActivityPause $actionable = null)
    {
        $this->taskAssignee = $taskAssignee;
        switch($action){
            case TaskTimerAction::START:
                $this->startTimer();
                break;
            case TaskTimerAction::PAUSE:
                $this->pauseTimer(activity: $actionable);
                break;
            case TaskTimerAction::RESUME:
                $this->resumeTimer(pause: $actionable);
                break;
            case TaskTimerAction::STOP:
                $this->stopTimer(activity: $actionable);
                break;
            case TaskTimerAction::RESET:
                $this->resetTimer(activity: $actionable);
                break;
        }
    }

    protected function startTimer(): Carbon{
        if ( !$this->taskAssignee->canStartTimer() ) throw new \Exception('You cannot start a timer');

        return $this->taskAssignee->activities()->create(['started_at' => $this->now])->started_at;
    }

    protected function pauseTimer(TaskActivity $activity): Carbon{
        if ( !$this->taskAssignee->canPauseTimer(activity: $activity) ) throw new \Exception('You cannot pause this timer');

        return $activity->pauses()->create(['paused_at' => $this->now])->paused_at;
    }

    protected function resumeTimer(TaskActivityPause $pause): Carbon{
        if ( !$this->taskAssignee->canResumeTimer(pause: $pause) ) throw new \Exception('You cannot resume this timer');

        $pause->update(['resumed_at' => $this->now]);
        return $pause->resumed_at;
    }

    protected function stopTimer(TaskActivity $activity): Carbon{
        if ( !$this->taskAssignee->canStopTimer(activity: $activity) ) throw new \Exception('You cannot stop this timer');

        $activity->update(['completed_at' => $this->now]);
        return $activity->completed_at;
    }

    protected function resetTimer(TaskActivity $activity): void{
        if ( !$this->taskAssignee->canResetTimer(activity: $activity) ) throw new \Exception('You cannot reset this timer');

        $activity->delete();
        $this->startTimer();
    }
}
