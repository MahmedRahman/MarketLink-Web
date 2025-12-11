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
            $table->json('brand_profile')->nullable()->after('content')->comment('Brand Profile: Voice, Tone, Structure, Language Style, CTA Style, Enemy, Values, Hook Patterns, Phrases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_style_extractors', function (Blueprint $table) {
            $table->dropColumn('brand_profile');
        });
    }
};
