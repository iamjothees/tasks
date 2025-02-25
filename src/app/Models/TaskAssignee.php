<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskAssignee extends Pivot
{
    use HasFactory;

    protected $appends = ['can_start_timer', 'can_pause_timer', 'can_resume_timer', 'can_stop_timer'];

    public function task(){
        return $this->belongsTo(Task::class);
    }

    public function activities(){
        return $this->hasMany( TaskActivity::class, 'task_assignee_id' ); 
    }

    public function latestActivity(){
        return $this->hasOne(TaskActivity::class, 'task_assignee_id')->latestOfMany('id'); 
    }

    public function latestActiveActivity(){
        return $this->hasOne(TaskActivity::class, 'task_assignee_id')->latestOfMany('id')->whereNull('completed_at'); 
    }

    public function oldestActivity(){
        return $this->oldestOfMany('id', TaskActivity::class, 'task_assignee_id'); 
    }

    public function getCanStartTimerAttribute(): bool{
        return $this->activities()->whereNull('completed_at')->doesntExist();
    }

    public function getCanPauseTimerAttribute(): bool{
        return $this->latestActiveActivity()->exists() && (
            $this->latestActiveActivity->pauses()->doesntExist() ||
            $this->latestActiveActivity->pauses()->whereNull('resumed_at')->doesntExist()
        );
    }

    public function getCanResumeTimerAttribute(): bool{
        return $this->latestActiveActivity()->exists() && $this->latestActiveActivity->pauses()->whereNull('resumed_at')->exists();
    }

    public function getCanStopTimerAttribute(): bool{
        return $this->latestActiveActivity()->exists();
    }
}
