<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    /** @use HasFactory<\Database\Factories\TaskTypeFactory> */
    use HasFactory;

    public function tasks(){
        return $this->hasMany(Task::class, 'type_id');
    }
}
