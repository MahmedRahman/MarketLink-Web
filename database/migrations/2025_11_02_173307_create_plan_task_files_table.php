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
        Schema::create('plan_task_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_task_id')->constrained('plan_tasks')->onDelete('cascade');
            $table->string('file_name'); // اسم الملف الأصلي
            $table->string('file_path'); // مسار الملف المخزن
            $table->string('file_type')->nullable(); // نوع الملف (pdf, docx, jpg, etc.)
            $table->integer('file_size')->nullable(); // حجم الملف بالبايت
            $table->text('description')->nullable(); // وصف الملف
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_task_files');
    }
};
