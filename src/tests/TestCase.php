<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public User $user;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
}
