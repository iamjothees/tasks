<?php

use App\Models\TaskType;
use Illuminate\Support\Facades\Auth;

pest()->group('dashboard');

it('prevents dashboard from guest', function () {
    // ACT & ASSERT
    Auth::logout();
    $this->get(route('filament.app.pages.dashboard'))->assertStatus(302);
});

it('opens dashboard', function () {
    // ACT && ASSERT
    $this->get(route('filament.app.pages.dashboard'))
        ->assertOk();
});

it('has menus', function () {
        
    // ACT && ASSERT
    $this->get(route('filament.app.pages.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        
        ->assertSee('Tasks')
        ->assertSee(TaskType::pluck('name'))

        ->assertSee('Task Configs')
        ->assertSee([ 'Types', 'Priorities', 'Statuses' ])
        
        ->assertSee('Settings')
        ->assertSee([ 'Task Settings' ]);
});
