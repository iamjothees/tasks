<?php

namespace App\Filament\Resources\TaskPriorityResource\Pages;

use App\Filament\Resources\TaskPriorityResource;
use App\Services\TaskPriorityService;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTaskPriorities extends ManageRecords
{
    protected static string $resource = TaskPriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('sm')
                ->using( fn (array $data, TaskPriorityService $service) => $service->store(data: $data), ),
        ];
    }
}
