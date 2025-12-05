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
        Schema::table('meetings', function (Blueprint $table) {
            // حذف foreign key القديم
            $table->dropForeign(['created_by']);
            // حذف العمود القديم
            $table->dropColumn('created_by');
            // إضافة عمود جديد يشير إلى employees
            $table->foreignId('responsible_employee_id')->nullable()->after('organization_id')->constrained('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            // حذف foreign key الجديد
            $table->dropForeign(['responsible_employee_id']);
            // حذف العمود الجديد
            $table->dropColumn('responsible_employee_id');
            // إعادة العمود القديم
            $table->foreignId('created_by')->nullable()->after('organization_id')->constrained('users')->onDelete('set null');
        });
    }
};
