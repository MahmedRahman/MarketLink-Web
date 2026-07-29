<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['organization_id', 'order']);
        });

        Schema::table('work_activities', function (Blueprint $table) {
            $table->foreignId('folder_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('work_folders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('folder_id');
        });

        Schema::dropIfExists('work_folders');
    }
};
