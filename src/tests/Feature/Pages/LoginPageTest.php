<?php

use Illuminate\Support\Facades\Auth;

pest()->group('login');

it('opens login page', function () {
    Auth::logout();
    // Act & Assert
    $this->get(route('filament.app.auth.login'))
        ->assertStatus(200)
        ->assertSee('Sign in');
});

it('opens login page only for guests', function () {
    // Act & Assert
    $this->get(route('filament.app.auth.login'))
        ->assertStatus(302);

    // Arrange
    Auth::logout();
    // Act & Assert
    $this->get(route('filament.app.auth.login'))
        ->assertStatus(200);


});
