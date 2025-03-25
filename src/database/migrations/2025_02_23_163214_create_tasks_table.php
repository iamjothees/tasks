<?php

use App\Enums\TaskRecursion;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskAssignee;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('type_id')->constrained('task_types');

            $table->tinyInteger('priority_level');
            $table->foreign('priority_level')->references('level')->on('task_priorities')->onDelete('restrict')->onUpdate('cascade');
            $table->tinyInteger('status_level');
            $table->foreign('status_level')->references('level')->on('task_statuses')->onDelete('restrict')->onUpdate('cascade');

            $table->timestamp('next_schedule_at')->nullable();
            $table->string('recursion')->default(TaskRecursion::NEVER); //  dynamic, once, daily, weekly, monthly , yearly

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('task_assignee', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'assignee_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
        
        Schema::create('task_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TaskAssignee::class, 'task_assignee_id')->constrained('task_assignee');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
        });

        Schema::create('task_activity_pauses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TaskActivity::class)->constrained()->cascadeOnDelete();
            $table->timestamp('paused_at');
            $table->timestamp('resumed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_activity_pauses');
        Schema::dropIfExists('task_activities');
        Schema::dropIfExists('task_assignee');
        Schema::dropIfExists('tasks');
    }
};
