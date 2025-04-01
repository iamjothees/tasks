<?php

namespace App\Providers;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();

        // Add your aliases
        $loader->alias('TaskTimerAction', \App\Enums\TaskTimerAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Context::add(key: 'now', value: now()->micro(0));

        Gate::define('act-on-task-timer', function (User $user, Task $task) {
            if ( $task->assignees()->where('assignee_id', $user->id)->doesntExist() ) return Response::deny();
            return Response::allow();
        });

        // Filament

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make('Tasks'),
                NavigationGroup::make('Task Configs')
                    ->icon('heroicon-o-wrench-screwdriver'),
                NavigationGroup::make('Settings')
                    ->icon('heroicon-o-cog-6-tooth'),
            ]);
        });

        Filament::serving(function () {
            Filament::registerNavigationItems(
                TaskType::get(['name', 'slug'])
                ->map(function (TaskType $type) {
                    return NavigationItem::make($type->name)
                        ->url(fn () => TaskResource::getUrl('index', ['type' => $type->slug]))
                        ->isActiveWhen(fn (): bool => request()->url() === TaskResource::getUrl('index', ['type' => $type->slug]))
                        ->group('Tasks');
                })
                ->prepend(
                    NavigationItem::make('All Tasks')
                        ->url(fn () => TaskResource::getUrl('index'))
                        ->isActiveWhen(fn (): bool => request()->url() === TaskResource::getUrl('index'))
                        ->group('Tasks')
                )
                ->toArray()
            );
        });
    }
}
