<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_activity_id');
            $table->unsignedBigInteger('assigned_to')->nullable(); // employees.id
            $table->string('title');
            $table->text('idea')->nullable();
            $table->text('notes')->nullable();
            $table->string('kind')->default('other'); // design | video | content | publish | other
            $table->string('status')->default('todo'); // todo | in_progress | review | done
            $table->date('due_date')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('work_activity_id')->references('id')->on('work_activities')->onDelete('cascade');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('kind');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_tasks');
    }
};
