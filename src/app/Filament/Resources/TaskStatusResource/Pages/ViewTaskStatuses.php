<?php

namespace App\Filament\Resources\TaskStatusResource\Pages;

use App\Filament\Resources\TaskStatusResource;
use App\Services\TaskStatusService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTaskStatuses extends ViewRecord
{
    protected static string $resource = TaskStatusResource::class;

    public function getHeaderActions(): array{
        return [
            EditAction::make()->modalWidth('md')
                ->using( fn ($record, $data, TaskStatusService $service) => $service->update(taskStatus: $record, data: $data)),
            DeleteAction::make()
                ->hidden(fn ($record) => $record->tasks()->count() > 0)
                ->action(fn ($record, TaskStatusService $service) => $service->delete(taskStatus: $record))
        ];
    }
}
