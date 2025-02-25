<?php

namespace App\Models;

use App\Enums\TaskRecursion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'recursion' => TaskRecursion::class,
        'next_schedule_at' => 'datetime:d-m-Y\Th:i:s a',
        'completed_at' => 'datetime:d-m-Y\Th:i:s a',
    ];

    public function assignees(){
        return $this->belongsToMany(User::class, 'task_assignee', 'task_id', 'assignee_id')
            ->using(TaskAssignee::class)
            ->withPivot(['id']);
    }
}
