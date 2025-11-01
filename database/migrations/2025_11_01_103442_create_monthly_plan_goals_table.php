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
        Schema::create('monthly_plan_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_plan_id')->constrained()->onDelete('cascade');
            $table->string('goal_type'); // مثل 'posts', 'designs', 'ads_budget'
            $table->string('goal_name'); // مثل 'نشر بوستات', 'عمل تصميمات', 'ميزانية إعلانات'
            $table->integer('target_value')->default(0); // القيمة المستهدفة
            $table->integer('achieved_value')->default(0); // القيمة المحققة
            $table->string('unit')->nullable(); // الوحدة مثل 'بوست', 'تصميم', 'جنيه'
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_plan_goals');
    }
};
