<?php

use Tests\TestCase;

pest()->uses(TestCase::class);
it('opens login page', function () {
    // Act & Assert
    $this->get(route('filament.app.auth.login'))
        ->assertStatus(200)
        ->assertSee('Sign in');
});

it('opens login page only for guests', function () {
    // Act & Assert
    $this->get(route('filament.app.auth.login'))
        ->assertStatus(200);

    // Arrange
    $this->actingAs($this->user);
    // Act & Assert
    $this->get(route('filament.app.auth.login'))
        ->assertStatus(302);


});
