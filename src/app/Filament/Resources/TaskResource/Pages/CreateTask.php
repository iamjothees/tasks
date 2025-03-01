<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    public function handleRecordCreation(array $data): Task{
        return app(TaskService::class)->store(data: $data, user: Auth::user());
    }
}
