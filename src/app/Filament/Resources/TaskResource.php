<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use App\Services\TaskPriorityService;
use App\Services\TaskStatusService;
use App\Settings\TaskSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static bool $shouldRegisterNavigation = false;

    public $type = null;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Select::make('priority_level')
                    ->relationship(name: 'priority', titleAttribute: 'name', modifyQueryUsing: fn ($query) => $query->orderByDesc('level'))
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->createOptionForm(TaskPriorityResource::getformSchema())
                    ->createOptionUsing(fn (array $data, TaskPriorityService $service) :int => $service->store(data: $data)->level )
                    ->createOptionModalHeading('Create Priority')
                    ->createOptionAction(fn ($action) => $action->modalWidth('md'))
                    ->default(fn (TaskSettings $settings) => $settings->default_priority_level)
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
                
                Forms\Components\Select::make('status_level')
                    ->relationship(name: 'status', titleAttribute: 'name', modifyQueryUsing: fn ($query) => $query->orderByDesc('level'))
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->createOptionForm(TaskStatusResource::getformSchema())
                    ->createOptionUsing(fn (array $data, TaskStatusService $service) :int => $service->store(data: $data)->level )
                    ->createOptionModalHeading('Create Status')
                    ->createOptionAction(fn ($action) => $action->modalWidth('md'))
                    ->default(fn (TaskSettings $settings) => $settings->default_status_level)
                    ->required(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/all/{type?}'),
            'create' => Pages\CreateTask::route('/{type?}/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->when(request()->route('type'), fn ($q, $type) => $q->where('type', $type))
            ->whereHas('assignees', fn ($q) => $q->where('assignee_id', Auth::id()))
            ->orderByDesc('priority_level');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                Infolists\Components\TextEntry::make('priority.name')
                    ->label('Priority')
                    ->badge()
                    ->color(fn ($record) => Color::hex($record->priority->color)),
                Infolists\Components\TextEntry::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => Color::hex($record->status->color)),
            ]);
    }
}
