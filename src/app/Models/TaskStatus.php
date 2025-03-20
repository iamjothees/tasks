<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    protected $primaryKey = 'level';

    public $incrementing = false;

    public static int $minLevel = -10;
    public static int $maxLevel = 100;

    public function tasks(){
        return $this->hasMany(Task::class, 'status_level', 'level');
    }
}
