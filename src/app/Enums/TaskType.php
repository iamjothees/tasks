<?php

namespace App\Enums;

enum TaskType: string
{
    case FEATURE = 'feature';
    case BUG = 'bug';
    case TASK = 'task';
    case DOCUMENTATION = 'documentation';
    case TESTING = 'testing';
    case REFACTORING = 'refactoring';
    case QC = 'qc';
    case REVIEW = 'review';
    case DESIGN = 'design';
    case IMPLEMENTATION = 'implementation';
    case DEPLOYMENT = 'deployment';
    case MAINTENANCE = 'maintenance';
    case OTHER = 'other';
    
    public function label(): string{
        return match ($this) {
            default => str($this->value)->title()->toString(),
        };
    }
}
