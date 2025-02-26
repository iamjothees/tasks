<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskActivityPause extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'paused_at' => 'datetime:d-m-Y\Th:i:s a',
        'resumed_at' => 'datetime:d-m-Y\Th:i:s a',
    ];
}
