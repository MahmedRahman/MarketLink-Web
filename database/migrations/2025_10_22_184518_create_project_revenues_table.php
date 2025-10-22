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
        Schema::create('project_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title'); // عنوان الإيراد
            $table->text('description')->nullable(); // وصف الإيراد
            $table->decimal('amount', 15, 2); // المبلغ
            $table->string('currency', 3)->default('EGP'); // العملة
            $table->date('revenue_date'); // تاريخ الإيراد
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'check', 'other'])->default('bank_transfer'); // طريقة الدفع
            $table->string('payment_reference')->nullable(); // مرجع الدفع (رقم التحويل، رقم الشيك، إلخ)
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending'); // حالة الإيراد
            $table->string('invoice_number')->nullable(); // رقم الفاتورة
            $table->date('invoice_date')->nullable(); // تاريخ الفاتورة
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_revenues');
    }
};