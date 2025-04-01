<?php

use App\Models\TaskType;
use App\Models\User;
use Tests\TestCase;

pest()->uses(TestCase::class);

it('prevents dashboard from guest', function () {
    // ACT & ASSERT
    $this->get(route('filament.app.pages.dashboard'))->assertStatus(302);
});

it('opens dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // ACT && ASSERT
    $this->get(route('filament.app.pages.dashboard'))
        ->assertOk();
});

it('has menus', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
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
