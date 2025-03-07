<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskStatusResource\Pages;
use App\Filament\Resources\TaskStatusResource\RelationManagers;
use App\Forms\Components\LevelSelector;
use App\Models\TaskStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskStatusResource extends Resource
{
    protected static ?string $model = TaskStatus::class;

    protected static ?string $navigationGroup = "Task Configs";
    protected static ?string $label = "Status";

    
    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema(self::getFormSchema());
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) =>  $query->orderBy('level', 'asc') )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color(fn ($record) => Color::hex($record->color))
                    ->description(fn ($record) => str($record->description)->limit(50))
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->numeric()
                    ->sortable()
                    ->width('100px'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime("h:i A d-m-Y")
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')
                ->dateTime("h:i A d-m-Y")
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Priority Details')
                ->schema([
                    Infolists\Components\Split::make([
                        Infolists\Components\TextEntry::make('name')
                            ->badge()->color(fn ($record) => Color::hex($record->color)),
                        Infolists\Components\TextEntry::make('level'),
                    ])->from('sm'),
                    Infolists\Components\TextEntry::make('description'),
                ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTaskStatuses::route('/'),
        ];
    }

    public static function getFormSchema(): array{
            return [
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),
                LevelSelector::make('level')
                    ->viewData([
                        'minValue' => TaskStatus::$minLevel,
                        'maxValue' => TaskStatus::$maxLevel,
                    ])
                    ->columnSpanFull()
                    ->default(0)
                    ->live()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\ColorPicker::make('color')
                    ->required()
                    ->default(sprintf('#%06X', mt_rand(0, 0xFFFFFF))),
            ];
    }
}
