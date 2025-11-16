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
        Schema::table('project_revenues', function (Blueprint $table) {
            $table->string('record_month_year', 7)->nullable()->after('revenue_date'); // تنسيق: Y-m (مثل: 2024-01)
        });

        Schema::table('project_expenses', function (Blueprint $table) {
            $table->string('record_month_year', 7)->nullable()->after('expense_date'); // تنسيق: Y-m (مثل: 2024-01)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_revenues', function (Blueprint $table) {
            $table->dropColumn('record_month_year');
        });

        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn('record_month_year');
        });
    }
};
