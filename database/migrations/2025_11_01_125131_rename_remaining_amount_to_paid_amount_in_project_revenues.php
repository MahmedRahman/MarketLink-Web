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
        Schema::table('project_revenues', function (Blueprint $table) {
            $table->renameColumn('remaining_amount', 'paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_revenues', function (Blueprint $table) {
            $table->renameColumn('paid_amount', 'remaining_amount');
        });
    }
};
