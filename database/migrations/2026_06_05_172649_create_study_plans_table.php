<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('skill');
            $table->string('level')->default('beginner');
            $table->integer('duration_weeks');
            $table->integer('hours_per_week');
            $table->text('description')->nullable();
            $table->json('questionnaire_data')->nullable();
            $table->json('ai_raw_response')->nullable();
            $table->string('status')->default('generating');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plans');
    }
};
