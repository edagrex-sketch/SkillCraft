<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanTopic extends Model
{
    protected $fillable = [
        'plan_week_id',
        'title',
        'description',
        'content_type',
        'resource_url',
        'estimated_minutes',
        'has_theory',
        'theory_content',
        'practice_description',
        'examples',
        'difficulty',
        'is_completed',
        'completed_at',
        'order',
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
    ];

    protected function casts(): array
    {
        return [
            'has_theory' => 'boolean',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'reflection_prompts' => 'array',
            'common_mistakes' => 'array',
            'analogies' => 'array',
            'prerequisites' => 'array',
            'review_questions' => 'array',
        ];
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(PlanWeek::class, 'plan_week_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(LearningSession::class, 'plan_topic_id');
    }
}
