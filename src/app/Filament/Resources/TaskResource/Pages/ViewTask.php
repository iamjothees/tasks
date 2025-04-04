<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskPriorityResource;
use App\Filament\Resources\TaskResource;
use App\Services\TaskPriorityService;
use App\Settings\TaskSettings;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.resources.tasks.pages.view-task';

    public function getHeading(): Htmlable
    {
        return new HtmlString("
            <div>
                <div class='flex items-center gap-3'>
                    <div class='h-6 w-6 rounded flex-shrink-0' style='background-color:{$this->record->priority->color}'></div>
                    {$this->record->title}
                </div>".
                    TextColumn::make("priority.name")->record($this->record)->badge()->color(fn ($record) => Color::hex($this->record->priority->color))->toHtml()
            ."</div>
        ");
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('')->icon('heroicon-o-bars-4')->outlined()
                ->extraAttributes([ 'class' => "!gap-0", ])
                ->url(null)->slideOver()->modalWidth('md')
                ->modalHeading(null)
                ->form([
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
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->save())
                ])
                ->modalFooterActions(null),
            Actions\DeleteAction::make()->label('')->link()->icon('heroicon-o-trash'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl('index') => 'Tasks',
            self::getResource()::getUrl('index', [$this->record->type]) => $this->record->type->name,
            'View',
        ];
    }

    public function description(Infolist $infolist): Infolist{
        return $infolist
            ->extraAttributes(['class'=> 'mt-3'])
            ->schema([
                Infolists\Components\Section::make('Description')
                 ->schema([
                     Infolists\Components\TextEntry::make('description')
                        ->label('')
                        ->html()
                 ])
            ]);
    }
}
