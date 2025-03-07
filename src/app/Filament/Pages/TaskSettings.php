<?php

namespace App\Filament\Pages;

use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Settings\TaskSettings AS Settings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class TaskSettings extends SettingsPage
{
    protected static ?string $navigationGroup = "Settings";

    protected static string $settings = Settings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('default_priority_level')
                    ->options(TaskPriority::pluck('name', 'level'))
                    ->native(false)
                    ->label('Default Priority')
                    ->required(),
                Forms\Components\Select::make('default_status_level')
                    ->options(TaskStatus::pluck('name', 'level'))
                    ->native(false)
                    ->label('Default Status')
                    ->required(),
            ]);
    }
}
