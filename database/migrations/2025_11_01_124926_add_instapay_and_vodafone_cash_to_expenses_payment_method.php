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
        // SQLite does not support MODIFY ENUM directly.
        // We will drop the column and re-add it as a string type.
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('project_expenses', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
            Schema::table('project_expenses', function (Blueprint $table) {
                $table->string('payment_method')->default('bank_transfer')->after('expense_date');
            });
        } else {
            // For other databases (e.g., MySQL), use MODIFY COLUMN
            DB::statement("ALTER TABLE project_expenses MODIFY payment_method ENUM('cash', 'bank_transfer', 'credit_card', 'check', 'vodafone_cash', 'instapay', 'other') DEFAULT 'bank_transfer'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payment_method for non-SQLite databases
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE project_expenses MODIFY payment_method ENUM('cash', 'bank_transfer', 'credit_card', 'check', 'other') DEFAULT 'bank_transfer'");
        }
    }
};
