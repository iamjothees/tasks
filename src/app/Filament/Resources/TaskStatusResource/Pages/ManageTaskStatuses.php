<?php

namespace App\Filament\Resources\TaskStatusResource\Pages;

use App\Filament\Resources\TaskStatusResource;
use App\Services\TaskStatusService;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTaskStatuses extends ManageRecords
{
    protected static string $resource = TaskStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->modalWidth('sm')
            ->using( fn (array $data, TaskStatusService $service) => $service->store(data: $data), ),
        ];
    }
}
