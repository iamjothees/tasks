<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('task.default_priority_level', '0');
        $this->migrator->add('task.default_status_level', '0');
    }
};
