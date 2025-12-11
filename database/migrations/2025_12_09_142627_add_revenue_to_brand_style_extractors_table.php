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
        Schema::table('brand_style_extractors', function (Blueprint $table) {
            $table->decimal('revenue', 15, 2)->nullable()->after('content');
            $table->string('currency', 3)->default('EGP')->after('revenue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_style_extractors', function (Blueprint $table) {
            $table->dropColumn(['revenue', 'currency']);
        });
    }
};
