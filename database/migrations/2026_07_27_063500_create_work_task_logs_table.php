<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_task_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_task_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // stage_changed | status_changed | assignee_changed | created | updated | ...
            $table->string('field')->nullable(); // pipeline_stage | status | assigned_to | ...
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->string('message');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('work_task_id')->references('id')->on('work_tasks')->onDelete('cascade');
            $table->index(['work_task_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_task_logs');
    }
};
