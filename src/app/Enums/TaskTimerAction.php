<?php

namespace App\Enums;

enum TaskTimerAction: string
{
    case START = "start";
    case PAUSE = "pause";
    case RESUME = "resume";
    case STOP = "stop";
    case RESET = "reset";

}
