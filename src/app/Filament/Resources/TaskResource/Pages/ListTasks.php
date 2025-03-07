<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Services\TaskService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Filament\Support\Colors\Color;
use Filament\Tables;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('lg')
                ->modalHeading(
                    fn () => 
                    app(HtmlString::class, [
                        'html' => '
                            <a wire:navigate href=\''.TaskResource::getUrl('create-page').'\' class="flex items-center gap-2 underline">
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



    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state): string => str($state)->limit(50))
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority_level')
                    ->formatStateUsing(fn ($record) => $record->priority->name)
                    ->badge()
                    ->color(fn (Task $record) => Color::hex($record->priority->color))
                    ->width(120)
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status_level')
                    ->options(fn () => TaskStatus::orderBy('level')->pluck('name','level'))
                    ->searchable()
                    ->width(120)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordUrl(null)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make('view-slideover')->slideOver()->modalWidth('lg')->extraAttributes(['class' => 'hidden'])
                        ->modalHeading(
                            fn ($record) => 
                            app(HtmlString::class, [
                                'html' => '
                                    <a wire:navigate href=\''.TaskResource::getUrl('view-page', [$record->id]).'\' class="flex items-center gap-2 underline">
                                        View Task 
                                        <x-filament::icon >
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                        </x-filament::icon > 
                                    </a>
                                '
                            ])
                        ),
                    Tables\Actions\ViewAction::make()->url(fn ($record) => TaskResource::getUrl('view-page', ['record' => $record->id])),
                    Tables\Actions\EditAction::make()->slideOver()->modalWidth('lg')
                        ->modalHeading(
                            fn ($record) => 
                            app(HtmlString::class, [
                                'html' => '
                                    <a wire:navigate href=\''.TaskResource::getUrl('edit-page', [$record->id]).'\' class="flex items-center gap-2 underline">
                                        Edit Task 
                                        <x-filament::icon >
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                        </x-filament::icon > 
                                    </a>
                                '
                            ])
                        ),
                ])
            ])
            ->recordAction('view-slideover')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ])->hidden(true),
            ]);
    }
}
