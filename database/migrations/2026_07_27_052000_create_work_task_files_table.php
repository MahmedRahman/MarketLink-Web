<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_task_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_task_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->string('asset_kind')->default('image'); // image | video | pdf
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable(); // users.id
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('work_task_id')->references('id')->on('work_tasks')->onDelete('cascade');
            $table->index('asset_kind');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_task_files');
    }
};
