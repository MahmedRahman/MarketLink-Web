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
        Schema::table('employees', function (Blueprint $table) {
            // تحديث enum للـ role لتشمل الأدوار الجديدة
            $table->enum('role', [
                'content_writer',      // كاتب محتوى
                'ad_manager',          // إدارة إعلانات
                'designer',            // مصمم
                'video_editor',        // مصمم فيديوهات
                'page_manager',        // إدارة الصفحة
                'account_manager',     // أكونت منجر
                'monitor',            // مونتير
                'media_buyer'         // ميديا بايرز
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // إرجاع enum للـ role إلى الحالة السابقة
            $table->enum('role', [
                'content_writer',      // كاتب محتوى
                'ad_manager',          // إدارة إعلانات
                'designer',            // مصمم
                'video_editor',        // مصمم فيديوهات
                'page_manager',        // إدارة الصفحة
                'account_manager'      // أكونت منجر
            ])->change();
        });
    }
};
