<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_activities', function (Blueprint $table) {
            $table->string('lecturer_name')->nullable()->after('description');
            $table->text('lecture_goals')->nullable()->after('lecturer_name');
            $table->string('lecture_time')->nullable()->after('event_date');
        });
    }

    public function down(): void
    {
        Schema::table('work_activities', function (Blueprint $table) {
            $table->dropColumn(['lecturer_name', 'lecture_goals', 'lecture_time']);
        });
    }
};
