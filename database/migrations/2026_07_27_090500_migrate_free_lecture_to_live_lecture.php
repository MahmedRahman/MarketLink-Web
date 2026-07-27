<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // إلغاء «محاضرة مجانية» ودمجها مع «محاضرة لايف مجانية»
        DB::table('work_activities')
            ->where('type', 'free_lecture')
            ->update(['type' => 'live_lecture']);
    }

    public function down(): void
    {
        // لا نرجّع تلقائيًا لأن التمييز بين المجاني القديم واللايف اتلغى
    }
};
