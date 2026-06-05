<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_week_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('content_type')->default('exercise');
            $table->text('theory_content')->nullable();
            $table->text('practice_description')->nullable();
            $table->text('examples')->nullable();
            $table->string('difficulty')->default('beginner');
            $table->integer('estimated_minutes')->default(30);
            $table->string('resource_url')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_topics');
    }
};
