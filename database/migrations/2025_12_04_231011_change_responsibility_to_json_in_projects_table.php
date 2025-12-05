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
        Schema::table('projects', function (Blueprint $table) {
            // تغيير نوع العمود من enum إلى JSON
            $table->json('responsibility')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // إعادة العمود إلى enum
            $table->enum('responsibility', ['full_management', 'media_buyer', 'account_manager', 'design'])->nullable()->change();
        });
    }
};
