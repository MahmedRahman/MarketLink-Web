<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_tasks', function (Blueprint $table) {
            $table->json('publish_links')->nullable()->after('platforms');
        });

        // تقسيم مرحلة النشر القديمة إلى «جاهز للنشر»
        DB::table('work_tasks')
            ->where('pipeline_stage', 'publish')
            ->update(['pipeline_stage' => 'ready_to_publish']);
    }

    public function down(): void
    {
        DB::table('work_tasks')
            ->whereIn('pipeline_stage', ['ready_to_publish', 'published'])
            ->update(['pipeline_stage' => 'publish']);

        Schema::table('work_tasks', function (Blueprint $table) {
            $table->dropColumn('publish_links');
        });
    }
};
