<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = TaskPriority::factory()->count(4)->state(new Sequence(
            ['name' => 'Urgent', 'level' => '100'],
            ['name' => 'High', 'level' => '80'],
            ['name' => 'Medium', 'level' => '50'],
            ['name' => 'Low', 'level' => '20'],
        ))->create();

        $statuses = TaskStatus::factory()->count(4)->state(new Sequence(
            ['name'=> 'Void','level'=> '-50'],
            ['name'=> 'TODO','level'=> '1'],
            ['name'=> 'In-Progress','level'=> '10'],
            ['name'=> 'QC','level'=> '80'],
            ['name'=> 'Completed','level'=> '100'],
        ))->create();

        $users = User::factory(10)->create();
        $users->push(User::first());

        Task::factory()->recycle([ $priorities, $statuses ])
            ->count(100)
            ->afterCreating(function (Task $task) use ($users) {
                $task->assignees()->attach($users->random(rand(1,5)));
            })
            ->create();
    }
}
