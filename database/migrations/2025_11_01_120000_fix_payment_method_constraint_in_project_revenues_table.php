<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // في SQLite، نحتاج لإعادة إنشاء الجدول لإزالة CHECK constraint القديم
            // لكن دعنا نستخدم طريقة أبسط: إعادة إنشاء العمود فقط
            
            // أولاً، احفظ البيانات الموجودة
            $revenues = DB::table('project_revenues')->get();
            
            // احذف الجدول وأعد إنشاءه
            Schema::dropIfExists('project_revenues');
            
            Schema::create('project_revenues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('amount', 15, 2);
                $table->string('currency', 3)->default('EGP');
                $table->date('revenue_date');
                // استخدم string بدلاً من enum في SQLite
                $table->string('payment_method')->default('bank_transfer');
                $table->string('payment_reference')->nullable();
                $table->string('transfer_image')->nullable();
                $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending');
                $table->string('invoice_number')->nullable();
                $table->date('invoice_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
            
            // أعد إدخال البيانات
            foreach ($revenues as $revenue) {
                DB::table('project_revenues')->insert((array) $revenue);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا حاجة لإعادة التعديل
    }
};

