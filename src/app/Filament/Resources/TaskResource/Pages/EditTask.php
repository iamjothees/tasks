<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl('index') => 'Tasks',
            self::getResource()::getUrl('index', [$this->record->type]) => $this->record->type->name,
            'Edit',
        ];
    }
}
