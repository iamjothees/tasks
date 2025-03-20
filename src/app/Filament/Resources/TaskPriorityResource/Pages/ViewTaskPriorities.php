<?php

namespace App\Filament\Resources\TaskPriorityResource\Pages;

use App\Filament\Resources\TaskPriorityResource;
use App\Services\TaskPriorityService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTaskPriorities extends ViewRecord
{
    protected static string $resource = TaskPriorityResource::class;

    public function getHeaderActions(): array{
        return [
            EditAction::make()->modalWidth('md')
                ->using( fn ($record, $data, TaskPriorityService $service) => $service->update(taskPriority: $record, data: $data)),
            DeleteAction::make()
                ->hidden(fn ($record) => $record->tasks()->count() > 0)
                ->action(fn ($record, TaskPriorityService $service) => $service->delete(taskPriority: $record))
        ];
    }
}
