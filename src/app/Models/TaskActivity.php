<?php

namespace App\Models;

use App\Services\TaskService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function latestPause(){
        return $this->hasOne( TaskActivityPause::class, 'task_activity_id'  )->latestOfMany('id'); 
    }

    public function pauses(){
        return $this->hasMany( TaskActivityPause::class, 'task_activity_id' ); 
    }

    public function getTimeTakenInSecondsAttribute(){
        return app(TaskService::class)->getActiveTimeInSeconds(activity: $this);
    }

    public function getIsCompletedAttribute(): bool{
        return (bool) $this->completed_at;
    }

    public function getIsActiveAttribute(): bool{
        return (bool) !$this->completed_at;
    }

    public function getIsPausedAttribute(): bool{
        return (bool)( $this->isActive && $this->latestPause?->isNotResumed );
    }

    public function getIsNeverPausedAttribute(): bool{
        return (bool) is_null($this->latestPause);
    }

    public function getIsAtleastOncePausedAttribute(): bool{
        return (bool) $this->latestPause;
    }
    
    public function getIsRunningAttribute(): bool{
        return (bool) ( $this->isActive && is_null($this->latestPause) || $this->latestPause->isResumed );
    }

    public function getIsLatestAttribute(): bool{
        $last_activity_id = Task::addSelect(['last_activity_id' => TaskActivity::select('id')->whereColumn('task_id', 'tasks.id')->latest('id')->limit(1)])->where('id', $this->task_id)->first()->last_activity_id;
        return (bool) ( $last_activity_id === $this->id );
    }
}
