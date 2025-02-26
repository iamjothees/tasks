<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Context;
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
    }
}
