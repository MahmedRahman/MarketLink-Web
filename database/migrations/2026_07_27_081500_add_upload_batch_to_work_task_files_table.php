<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_task_files', function (Blueprint $table) {
            $table->string('upload_batch')->nullable()->after('nas_synced_at');
            $table->string('nas_folder')->nullable()->after('upload_batch');
            $table->index('upload_batch');
        });
    }

    public function down(): void
    {
        Schema::table('work_task_files', function (Blueprint $table) {
            $table->dropIndex(['upload_batch']);
            $table->dropColumn(['upload_batch', 'nas_folder']);
        });
    }
};
