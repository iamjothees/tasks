<?php

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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->default('');
            $table->timestamp('next_schedule_at')->nullable();
            $table->string('recursion')->nullable(); //  dynamic, once, daily, weekly, monthly , yearly
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('task_assignee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->constrained('users')->cascadeOnDelete();
            
        });
        
        Schema::create('task_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignee_id')->constrained('task_assignee')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
        });

        Schema::create('task_activity_pauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_activity_id')->constrained()->onDelete('cascade');
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
