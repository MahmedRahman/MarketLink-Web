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
        // إضافة حقل صورة التحويل فقط إذا لم يكن موجوداً
        if (!Schema::hasColumn('project_revenues', 'transfer_image')) {
            Schema::table('project_revenues', function (Blueprint $table) {
                $table->string('transfer_image')->nullable()->after('payment_reference');
            });
        }

        // SQLite لا يدعم MODIFY مباشرة، لذلك سنستخدم طريقة مختلفة
        // سنجعل payment_method nullable مؤقتاً ثم نضيف القيم الجديدة
        if (DB::getDriverName() === 'sqlite') {
            // التحقق من نوع العمود الحالي
            $columns = DB::select("PRAGMA table_info(project_revenues)");
            $paymentMethodColumn = collect($columns)->firstWhere('name', 'payment_method');
            
            // إذا كان العمود موجوداً وكان من نوع enum (string في SQLite)
            if ($paymentMethodColumn && $paymentMethodColumn->type === 'varchar') {
                // العمود موجود بالفعل كـ string، لا حاجة للتعديل
            } else {
                // إعادة إنشاء العمود كـ string
                Schema::table('project_revenues', function (Blueprint $table) {
                    $table->dropColumn('payment_method');
                });
                
                Schema::table('project_revenues', function (Blueprint $table) {
                    $table->string('payment_method')->default('bank_transfer')->after('revenue_date');
                });
            }
        } else {
            // MySQL/MariaDB
            DB::statement("ALTER TABLE project_revenues MODIFY payment_method ENUM('cash', 'bank_transfer', 'credit_card', 'check', 'vodafone_cash', 'instapay', 'paypal', 'western_union', 'other') DEFAULT 'bank_transfer'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('project_revenues', 'transfer_image')) {
            Schema::table('project_revenues', function (Blueprint $table) {
                $table->dropColumn('transfer_image');
            });
        }

        // لا نحتاج لإعادة التعديل لأن SQLite يستخدم string
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE project_revenues MODIFY payment_method ENUM('cash', 'bank_transfer', 'credit_card', 'check', 'other') DEFAULT 'bank_transfer'");
        }
    }
};
