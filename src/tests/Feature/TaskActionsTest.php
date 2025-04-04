<?php

use App\Actions\Tasks\CalculateNextScheduleAtAction;
use App\Enums\TaskRecursion;
use App\Models\Task;
use Illuminate\Support\Carbon;

it('calculates_next_schedule_at', function () {
    // Arrange
    $task = Task::factory()->create([
        'next_schedule_at' => Carbon::parse('12-12-2012'),
        'recursion' => TaskRecursion::MONTHLY
    ]);


    $task2 = Task::factory()->create([
        'next_schedule_at' => Carbon::parse('12-12-2010'),
        'recursion' => TaskRecursion::YEARLY
    ]);

    // Act & Assert
    expect(app(CalculateNextScheduleAtAction::class, ['task' => $task])->execute())
        ->toEqual(Carbon::parse('12-01-2013'));

    expect(app(CalculateNextScheduleAtAction::class, ['task' => $task2])->execute())
        ->toEqual(Carbon::parse('12-12-2011'));
});
