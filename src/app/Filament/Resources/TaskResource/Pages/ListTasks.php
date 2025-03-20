<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Services\TaskService;
use App\Tables\Columns\TaskPrioritySwitcher;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Filament\Tables;
use Filament\Resources\Components\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(null)            
                ->slideOver()
                ->modalWidth('lg')
                ->modalHeading(
                    fn () => 
                    app(HtmlString::class, [
                        'html' => '
                            <a wire:navigate href=\''.TaskResource::getUrl('create').'\' class="flex items-center gap-2 underline">
                                Create Task 
                                <x-filament::icon >
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                </x-filament::icon > 
                            </a>
                        '
                    ])
                )
                ->using(fn (array $data, TaskService $taskService) => $taskService->store(data: $data, user: Auth::user()))
        ];
    }

    public function getTabs(): array
    {
        if ( ($this->table ?? null) && $this->getTable()->getActiveFiltersCount()) return [];

        return TaskStatus::withCount(['tasks' => fn ($q) => $q->whereRelation('assignees', 'assignee_id', Auth::id()) ])->orderBy('level')->get(['name', 'level', 'color'])
            ->keyBy('name')
            ->map(
                fn ($status) =>
                Tab::make($status->name)
                    ->badge($status->tasks_count)
                    ->badgeColor(Color::hex($status->color))
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status_level', $status->level))
            )->toArray();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority_level')
                    ->formatStateUsing(fn ($record) => $record->priority->name)
                    ->badge()
                    ->color(fn (Task $record) => Color::hex($record->priority->color))
                    ->width(120)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status_level')
                    ->options(fn () => TaskStatus::orderBy('level')->pluck('name','level'))
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('priority_level')
                    ->options(fn () => TaskPriority::orderBy('level')->pluck('name','level'))
                    ->label('Priority'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ViewAction::make('view-slideover')
                        ->url(null)->slideOver()->modalWidth('lg')
                        ->extraAttributes(['class' => 'hidden'])
                        ->modalHeading(
                            fn ($record) => 
                            app(HtmlString::class, [
                                'html' => '
                                    <a wire:navigate href=\''.self::getResource()::getUrl('view', [$record->id]).'\' class="flex items-center gap-2 underline">
                                        View Task 
                                        <x-filament::icon >
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                        </x-filament::icon > 
                                    </a>
                                '
                            ])
                        ),
                    Tables\Actions\EditAction::make('edit-slideover')
                        ->url(null)->slideOver()->modalWidth('lg')
                        ->modalHeading(
                            fn ($record) => 
                            app(HtmlString::class, [
                                'html' => '
                                    <a wire:navigate href=\''.self::getResource()::getUrl('edit', [$record->id]).'\' class="flex items-center gap-2 underline">
                                        Edit Task 
                                        <x-filament::icon >
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                        </x-filament::icon > 
                                    </a>
                                '
                            ])
                        )
                        ->action(
                            function (array $data, $record, $livewire){
                                $record->update($data);
                                $livewire->js('location.reload()');
                            }
                        ),
                ])
            ])
            ->recordUrl(null)
            ->recordAction(fn ($record) => Auth::user()->can('update', $record) ? 'edit-slideover' : 'view-slideover')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ])->hidden(true),
            ]);
    }
}
