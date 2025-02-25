<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskTimerAction;
use App\Exceptions\Task\NotInSyncException;
use App\Exceptions\TaskException;
use App\Models\Task;
use App\Models\TaskActivityPause;
use App\Models\TaskActivity;
use App\Models\TaskAssignee;
use App\Services\TaskService;
use Carbon\CarbonInterval;
use Livewire\Attributes\On;
use Livewire\Component;

class Timer extends Component
{
    public Task $task;
    public TaskAssignee $taskAssignee;
    public CarbonInterval $latestTimeTaken;

    public function mount(){
        $this->taskAssignee = $this->task->assignees()->where('assignee_id', auth()->id())->first()->pivot;
    }
    
    public function render(){
        return view('livewire.tasks.timer');
    }

    public function action(
        TaskTimerAction $action, 
        ?int $actionableModelId = null, // Task / TaskActivity / Pause
        ?string $actionableModelType = null
    ){
        try {
            $this->authorize('action', $this->taskAssignee);
        } catch (\Throwable $th) {
            $this->dispatch('warn', message: "Unauthorized");
            return;
        }
        $actionableModelId ??= $this->taskAssignee->id;
        if(is_null(
            $actionableModel = $this->validateAndRetrieveActionableModel(action: $action, modelId: $actionableModelId, modelType: $actionableModelType)
        )){
            session()->flash('invalid-input', "Invalid request.");
            return;
        }
        try {
            (new TaskService())->action(action: $action, actionableModel: $actionableModel);
        } catch( NotInSyncException $notInSyncException ){
            $this->taskAssignee->refresh();
            session()->flash(key: 'error', value: $notInSyncException->getUserMessage());
        }catch (TaskException $taskException) {
            $this->taskAssignee->refresh();
            session()->flash(key: 'error', value: $taskException->getUserMessage());
        } catch ( \Throwable $th ){
            if ( config('app.debug', false) ){
                dd($th);
            }
            session()->flash(key: 'error', value: "Oops! Something went wrong.");
        } finally {
            $this->taskAssignee->load([
                'category',
                'latestActivity',
                'latestActivity.latestPause'
            ]);
            $this->dispatch('refresh-task');
        }
    }

    // validate if actionable model is of current task
    protected function validateAndRetrieveActionableModel(TaskTimerAction $action, int $modelId, ?string $modelType = null): Task|TaskActivity|TaskActivityPause|null{
        switch ($action) {
            case TaskTimerAction::START:
                return $this->taskAssignee->id === $modelId ? $this->taskAssignee : null;
            case TaskTimerAction::PAUSE:
                if ($modelType === 'pause'){
                    $pause = TaskActivityPause::with(['activity'])->find($modelId);
                    return $this->taskAssignee->id === $pause?->activity->task_id ? $pause : null;
                }else if ($modelType === 'activity'){
                    $activity = TaskActivity::find($modelId);
                    return $this->taskAssignee->id === $activity?->task_id ? $activity : null;
                }
            case TaskTimerAction::RESUME:
                $pause = TaskActivityPause::with(['activity'])->find($modelId);
                return $this->taskAssignee->id === $pause?->activity->task_id ? $pause : null;
            case TaskTimerAction::STOP:
                $activity = TaskActivity::find($modelId);
                return $this->taskAssignee->id === $activity?->task_id ? $activity : null;
            default: return null;
        };
    }

    public function updateLatestTimeTaken(?int $activityId): string|null{
        $this->taskAssignee->refresh()->load([
            'latestActivity',
            'latestActivity.latestPause'
        ]);
        if (is_null($activityId) && !$this->taskAssignee->latestActivity?->isActive) return null;
        $activity = TaskActivity::find($activityId ?? $this->taskAssignee->latestActivity->id);
        return CarbonInterval::seconds($activity->timeTakenInSeconds)->cascade();
    }

    #[On('refresh-task-{task.id}')]
    public function refreshTask(): void{
        $this->taskAssignee->refresh();
    }
}
