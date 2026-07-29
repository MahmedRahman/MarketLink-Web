<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_ideas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->cascadeOnDelete();

            $table->string('title', 255);
            $table->text('description')->nullable();

            // قيم الأنواع لازم تطابق enum الموجود في WorkActivity (كله حرفي).
            $table->string('type', 50)->nullable();

            // suggested | archived
            $table->string('status', 30)->default('suggested');

            // صاحب الفكرة (web user أو employee)
            $table->string('creator_type', 20); // web | employee
            $table->unsignedBigInteger('creator_id');

            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['creator_type', 'creator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_ideas');
    }
};

