<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_topics', function (Blueprint $table) {
            $table->text('session_context')->nullable()->after('examples');
            $table->text('active_recall_question')->nullable()->after('session_context');
            $table->text('practice_guided')->nullable()->after('active_recall_question');
            $table->text('practice_structured')->nullable()->after('practice_guided');
            $table->text('practice_open')->nullable()->after('practice_structured');
            $table->json('reflection_prompts')->nullable()->after('practice_open');
            $table->json('common_mistakes')->nullable()->after('reflection_prompts');
            $table->text('challenge')->nullable()->after('common_mistakes');
            $table->json('analogies')->nullable()->after('challenge');
            $table->json('prerequisites')->nullable()->after('analogies');
            $table->json('review_questions')->nullable()->after('prerequisites');
        });
    }

    public function down(): void
    {
        Schema::table('plan_topics', function (Blueprint $table) {
            $table->dropColumn([
                'session_context',
                'active_recall_question',
                'practice_guided',
                'practice_structured',
                'practice_open',
                'reflection_prompts',
                'common_mistakes',
                'challenge',
                'analogies',
                'prerequisites',
                'review_questions',
            ]);
        });
    }
};
