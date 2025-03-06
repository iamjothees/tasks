<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\User;
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
    }
}
