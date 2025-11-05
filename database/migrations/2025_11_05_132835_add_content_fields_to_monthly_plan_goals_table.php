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
            $table->integer('carousel')->default(0)->after('target_value');
            $table->integer('reels')->default(0)->after('carousel');
            $table->integer('video')->default(0)->after('reels');
            $table->integer('photo')->default(0)->after('video');
            $table->dropColumn('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_plan_goals', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('target_value');
            $table->dropColumn(['carousel', 'reels', 'video', 'photo']);
        });
    }
};
