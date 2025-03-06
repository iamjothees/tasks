<?php

namespace App\Models;

use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskAssignee extends Pivot
{
    use HasFactory;

    public function getActivePauseAttribute(): ?TaskActivityPause{
        return $this->activeActivity?->activePause;
    }

    public function task(){
        return $this->belongsTo(Task::class);
    }

    public function assignee(){
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function activities(){
        return $this->hasMany( TaskActivity::class, 'task_assignee_id' ); 
    }

    public function latestActivity(){
        return $this->hasOne(TaskActivity::class, 'task_assignee_id')->latestOfMany('id'); 
    }

    public function activeActivity(){
        return $this->latestActivity()->whereNull('completed_at'); 
    }

    public function latestCompletedActivity(){
        return $this->latestActivity()->whereNotNull('completed_at');
    }

    public function oldestActivity(){
        return $this->oldestOfMany('id', TaskActivity::class, 'task_assignee_id'); 
    }

    public function canStartTimer(): bool{
        return 
        $this->activeActivity()->doesntExist()
        &&  app(UserService::class)->getActiveTaskActivities($this->assignee)->isEmpty();
    }

    public function canPauseTimer(?TaskActivity $activity = null): bool{
        return 
            $activity && $this->activeActivity()->exists() 
            && $this->activeActivity()->first('id')->id === $activity->id
            && $this->activeActivity->activePause()->doesntExist();
    }

    public function canResumeTimer(?TaskActivityPause $pause = null): bool{
        return 
            $pause && $this->activeActivity?->activePause()->exists()
            && $this->activeActivity->activePause()->first('id')->id === $pause->id;
    }

    public function canStopTimer(?TaskActivity $activity = null): bool{
        return 
            $activity && $this->activeActivity()->exists() 
            && $this->activeActivity()->first('id')->id === $activity->id;
    }

    public function canResetTimer(?TaskActivity $activity = null): bool{
        return $this->canStopTimer(activity: $activity);
    }
}
