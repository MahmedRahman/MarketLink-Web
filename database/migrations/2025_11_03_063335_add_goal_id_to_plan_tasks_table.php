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
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->foreignId('goal_id')->nullable()->after('monthly_plan_id')->constrained('monthly_plan_goals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropForeign(['goal_id']);
            $table->dropColumn('goal_id');
        });
    }
};
