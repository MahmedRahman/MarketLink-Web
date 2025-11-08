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
        // التحقق من وجود بيانات مكررة قبل إضافة unique constraint
        $duplicates = DB::table('employees')
            ->select('phone', DB::raw('COUNT(*) as count'))
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->groupBy('phone')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->count() > 0) {
            throw new \Exception('يوجد أرقام هواتف مكررة في قاعدة البيانات. يرجى حذف التكرارات أولاً قبل إضافة unique constraint.');
        }

        // إضافة unique constraint على phone
        Schema::table('employees', function (Blueprint $table) {
            $table->unique('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['phone']);
        });
    }
};
