<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_task_files', function (Blueprint $table) {
            $table->string('nas_path')->nullable()->after('description');
            $table->timestamp('nas_synced_at')->nullable()->after('nas_path');
        });
    }

    public function down(): void
    {
        Schema::table('work_task_files', function (Blueprint $table) {
            $table->dropColumn(['nas_path', 'nas_synced_at']);
        });
    }
};
