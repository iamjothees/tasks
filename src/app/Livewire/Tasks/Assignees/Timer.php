<?php

namespace App\Livewire\Tasks\Assignees;

use App\Enums\TaskTimerAction;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskActivityPause;
use App\Models\TaskAssignee;
use App\Models\User;
use App\Service\TaskTimerService;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Timer extends Component
{

    public Task $task;
    public TaskAssignee $taskAssignee;

    public function mount(?User $assignee = null){
        $this->taskAssignee = $this->task->assignees()->where('assignee_id', $assignee?->id ?? Auth::id())->first()->pivot;
        $this->refreshTaskAssignee();
    }

    public function render(){
        return view('livewire.tasks.assignees.timer');
    }

    public function act(TaskTimerAction $action, int $actionableId = null): TaskAssignee|null{
        $actionable = match ($action) {
            TaskTimerAction::START => null,
            TaskTimerAction::PAUSE => TaskActivity::find($actionableId),
            TaskTimerAction::RESUME => TaskActivityPause::find($actionableId),
            TaskTimerAction::STOP, TaskTimerAction::RESET => TaskActivity::find($actionableId),
        };
        try {
            app(TaskTimerService::class)->act(taskAssignee: $this->taskAssignee, action: $action, actionable: $actionable);
            $this->refreshTaskAssignee();
            if ($action === TaskTimerAction::STOP) $this->js('location.reload()');
        } catch (\Throwable $th) {
            config('app.env') === 'local' && throw $th;
            return null;
        }
        return $this->refreshTaskAssignee();
    }


    ## Helpers
    public function canStart(): bool{
        return $this->taskAssignee->canStartTimer();
    }

    public function canPause(?TaskActivity $activity = null): bool{
        return $this->taskAssignee->canPauseTimer(activity: $activity);
    }

    public function canResume(?TaskActivityPause $pause = null): bool{
        return $this->taskAssignee->canResumeTimer(pause: $pause);
    }

    public function canStop(?TaskActivity $activity = null): bool{
        return $this->taskAssignee->canStopTimer(activity: $activity);
    }

    public function refreshTaskAssignee(): TaskAssignee{
        return $this->taskAssignee->refresh()->load('activeActivity.activePause', 'latestActivity');
    }
}
