<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Joe',
            'email' => 'joe@joecodes.in',
            'password' => 'joe@123'
        ]);

        TaskPriority::create(['name' => 'default', 'level' => 0, 'color' => '#b8e6fe']);
        TaskStatus::create(['name' => 'default', 'level' => 0, 'color'=> '#b8e6fe']);

        if (config('app.env')) $this->call(DevSeeder::class);

        // Task::factory()->create()->assignees()->attach($user->id);
    }
}
