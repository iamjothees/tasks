<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskType;
use App\Services\TaskService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    public ?TaskType $type = null;

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl('index') => 'Tasks',
            ...(
                    $this->type
                        ? [self::getResource()::getUrl('index', [$this->type->slug]) => $this->type->name]
                        : []
            ),
            'Create',
        ];
    }

    public function handleRecordCreation(array $data): Task{
        return app(TaskService::class)->store(data: $data, user: Auth::user());
    }
}
