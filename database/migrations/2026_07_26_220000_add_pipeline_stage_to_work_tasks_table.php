<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_tasks', function (Blueprint $table) {
            $table->string('pipeline_stage')->default('writing')->after('status'); // writing | design | publish
            $table->index('pipeline_stage');
        });
    }

    public function down(): void
    {
        Schema::table('work_tasks', function (Blueprint $table) {
            $table->dropIndex(['pipeline_stage']);
            $table->dropColumn('pipeline_stage');
        });
    }
};
