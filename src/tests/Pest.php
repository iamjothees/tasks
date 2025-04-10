<?php

use App\Models\User;

uses(Tests\TestCase::class)->in('Feature')
->beforeEach(function () {
    loginAsUser();
});

pest()->group('layout')->in('Feature/Pages/Layout');
pest()->group('pages')->in('Feature/Pages');
pest()->group('tasks')->in('Feature/Pages/Tasks');

function loginAsUser(?User $user = null): User{
    $user ??= User::factory()->create();
    test()->actingAs($user);
    return $user;
}