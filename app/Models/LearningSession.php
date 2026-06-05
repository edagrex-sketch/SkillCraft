<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningSession extends Model
{
    protected $fillable = [
        'user_id',
        'study_plan_id',
        'plan_topic_id',
        'duration_minutes',
        'notes',
        'confidence_level',
        'started_at',
        'completed_at',
        'phase_completions',
        'reflection_answers',
        'active_recall_score',
        'needs_review',
        'next_review_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'next_review_at' => 'datetime',
            'needs_review' => 'boolean',
            'phase_completions' => 'array',
            'reflection_answers' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(PlanTopic::class, 'plan_topic_id');
    }
}
