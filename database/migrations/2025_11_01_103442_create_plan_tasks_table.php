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
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('title'); // عنوان المهمة
            $table->text('description')->nullable(); // وصف المهمة
            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo');
            $table->enum('list_type', ['tasks', 'employee'])->default('tasks'); // نوع القائمة: tasks أو employee (اسم الموظف)
            $table->integer('order')->default(0); // ترتيب المهمة في القائمة
            $table->date('due_date')->nullable(); // تاريخ الانتهاء
            $table->json('task_data')->nullable(); // بيانات إضافية للمهمة (JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};
