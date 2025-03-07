<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TaskSettings extends Settings
{

    public int $default_priority_level;
    public int $default_status_level;

    public static function group(): string
    {
        return 'task';
    }
}