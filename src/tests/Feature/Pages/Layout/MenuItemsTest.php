<?php

use App\Models\TaskType;

use function Pest\Laravel\{get};


pest()
->group('menu items')
->beforeEach(function () {
    loginAsUser();
});


it('has dashboard', function () {
    get('/')
    ->assertOk()
    ->assertSee('Dashboard');
});

it('has tasks by types', function () {
    get('/')
    ->assertOk()
    ->assertSee('Tasks')
    ->assertSee('All Tasks')
    ->assertSee(TaskType::pluck('name')->toArray());
});

it('has task configs', function () {
    get('/')
    ->assertOk()
    ->assertSee('Task Configs')
    ->assertSee(['Types', 'Priorities', 'Statuses']);    
});

it('has settings', function () {
    get('/')
    ->assertOk()
    ->assertSee('Settings')
    ->assertSee(['Task Settings']);
});