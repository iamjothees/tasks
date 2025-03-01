<?php

namespace App\Enums;

enum TaskRecursion: string
{
    case NONE = 'none';
    case NEVER = 'never';
    case CUSTOM = 'custom';

    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
