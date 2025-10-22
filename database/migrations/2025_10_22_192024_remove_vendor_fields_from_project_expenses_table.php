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
        Schema::table('project_expenses', function (Blueprint $table) {
            // إزالة الحقول المتعلقة بالمورد
            $table->dropColumn(['vendor_name', 'vendor_contact', 'receipt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // إعادة إضافة الحقول في حالة الرجوع
            $table->string('vendor_name')->nullable();
            $table->string('vendor_contact')->nullable();
            $table->string('receipt_number')->nullable();
        });
    }
};