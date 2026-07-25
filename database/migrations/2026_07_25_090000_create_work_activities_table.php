<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('title');
            $table->string('type')->default('other'); // live_lecture | paid_round | educational | other
            $table->text('description')->nullable();
            $table->date('event_date')->nullable();
            $table->string('status')->default('planning'); // planning | in_progress | done | cancelled
            $table->timestamps();

            $table->index('organization_id');
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_activities');
    }
};
