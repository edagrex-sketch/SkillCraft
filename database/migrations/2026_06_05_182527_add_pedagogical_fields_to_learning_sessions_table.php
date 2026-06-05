<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->json('phase_completions')->nullable()->after('confidence_level');
            $table->json('reflection_answers')->nullable()->after('phase_completions');
            $table->integer('active_recall_score')->nullable()->after('reflection_answers');
            $table->boolean('needs_review')->default(false)->after('active_recall_score');
            $table->timestamp('next_review_at')->nullable()->after('needs_review');
        });
    }

    public function down(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'phase_completions',
                'reflection_answers',
                'active_recall_score',
                'needs_review',
                'next_review_at',
            ]);
        });
    }
};
