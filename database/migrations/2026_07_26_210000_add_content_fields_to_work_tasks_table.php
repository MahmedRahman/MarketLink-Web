<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_tasks', function (Blueprint $table) {
            $table->text('tov')->nullable()->after('idea');
            $table->text('caption')->nullable()->after('tov');
            $table->string('content_type')->nullable()->after('caption'); // post | reels | carousel
            $table->text('design_reference')->nullable()->after('content_type');
            $table->unsignedBigInteger('content_writer_id')->nullable()->after('assigned_to');
            $table->unsignedBigInteger('designer_id')->nullable()->after('content_writer_id');
            $table->json('platforms')->nullable()->after('design_reference');
            $table->date('publish_date')->nullable()->after('due_date');

            $table->index('content_writer_id');
            $table->index('designer_id');
            $table->index('content_type');
        });
    }

    public function down(): void
    {
        Schema::table('work_tasks', function (Blueprint $table) {
            $table->dropIndex(['content_writer_id']);
            $table->dropIndex(['designer_id']);
            $table->dropIndex(['content_type']);
            $table->dropColumn([
                'tov',
                'caption',
                'content_type',
                'design_reference',
                'content_writer_id',
                'designer_id',
                'platforms',
                'publish_date',
            ]);
        });
    }
};
