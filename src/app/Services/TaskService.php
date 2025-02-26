<?php

namespace App\Services;

use App\Models\TaskActivity;
use App\Models\TaskAssignee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;

class TaskService
{
    protected Carbon $now;

    public function __construct()
    {
        $this->now = Context::get('now', now()->micro(0));
    }

    public function getActiveTimeInSeconds(TaskActivity $activity): float{
        $activity->load('pauses');
        $totalTimeTakenInSecs = $activity->started_at->diffInSeconds( $activity->completed_at ?? $this->now);
        $totalBreaks = $activity->pauses->sum( function ( $pause ) use ($activity){
            return $pause->paused_at->diffInSeconds($pause->resumed_at ?? $activity->completed_at ?? $this->now);
        });
        $activeTime = $totalTimeTakenInSecs - $totalBreaks;
        return $activeTime;
    }
}
