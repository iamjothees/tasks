<?php

namespace App\Models;

use App\Enums\TaskRecursion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'recursion' => TaskRecursion::class,
        'next_schedule_at' => 'datetime:d-m-Y\Th:i:s a',
        'completed_at' => 'datetime:d-m-Y\Th:i:s a',
    ];

    public function scopeActive($query){
        return $query->whereNull('completed_at');
    }

    public function assignees(){
        return $this->belongsToMany(User::class, 'task_assignee', 'task_id', 'assignee_id')
            ->using(TaskAssignee::class)
            ->withPivot(['id']);
    }

    public function assigneesPivots(){
        return $this->hasMany(TaskAssignee::class, 'assignee_id');
    }

    public function authAssigneePivot(){
        return $this->hasOne(TaskAssignee::class, 'assignee_id')->where('assignee_id', Auth::id());
    }

    public function type(){
        return $this->belongsTo(TaskType::class,'type_id');
    }

    public function priority(){
        return $this->belongsTo(TaskPriority::class,'priority_level','level');
    }

    public function status(){
        return $this->belongsTo(TaskStatus::class,'status_level','level');
    }
}
