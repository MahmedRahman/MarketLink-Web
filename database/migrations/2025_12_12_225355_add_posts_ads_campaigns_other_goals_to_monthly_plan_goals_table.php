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
        Schema::table('monthly_plan_goals', function (Blueprint $table) {
            $table->integer('posts')->default(0)->after('target_value');
            $table->integer('ads_campaigns')->default(0)->after('photo');
            $table->integer('other_goals')->default(0)->after('ads_campaigns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_plan_goals', function (Blueprint $table) {
            $table->dropColumn(['posts', 'ads_campaigns', 'other_goals']);
        });
    }
};
