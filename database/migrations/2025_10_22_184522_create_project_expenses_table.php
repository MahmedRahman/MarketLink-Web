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
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title'); // عنوان المصروف
            $table->text('description')->nullable(); // وصف المصروف
            $table->decimal('amount', 15, 2); // المبلغ
            $table->string('currency', 3)->default('EGP'); // العملة
            $table->date('expense_date'); // تاريخ المصروف
            $table->enum('category', ['marketing', 'advertising', 'design', 'development', 'content', 'tools', 'subscriptions', 'other']); // فئة المصروف
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'check', 'other'])->default('bank_transfer'); // طريقة الدفع
            $table->string('payment_reference')->nullable(); // مرجع الدفع
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending'); // حالة المصروف
            $table->string('vendor_name')->nullable(); // اسم المورد/المزود
            $table->string('vendor_contact')->nullable(); // معلومات الاتصال بالمورد
            $table->string('receipt_number')->nullable(); // رقم الإيصال
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};