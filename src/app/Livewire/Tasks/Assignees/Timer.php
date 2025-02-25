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
    }

    public function render(){
        return view('livewire.tasks.assignees.timer');
    }

    public function act(TaskTimerAction $action, TaskActivity|TaskActivityPause $actionable): bool{
        try {
            app(TaskTimerService::class)->act(taskAssignee: $this->taskAssignee, action: $action);
        } catch (\Throwable $th) {
            config('app.env') === 'local' && throw $th;
            return false;
        }
        return true;
    }
}
