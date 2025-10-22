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
        Schema::table('projects', function (Blueprint $table) {
            // إضافة حقل الأشخاص الموثقين كـ JSON
            $table->json('authorized_persons')->nullable()->comment('الأشخاص الموثقين - JSON array');
            
            // إضافة حقل الحسابات الخاصة بالمشروع كـ JSON
            $table->json('project_accounts')->nullable()->comment('الحسابات الخاصة بالمشروع - JSON array');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // إزالة الحقول المضافة
            $table->dropColumn(['authorized_persons', 'project_accounts']);
        });
    }
};
