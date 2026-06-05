<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyPlan extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'skill',
        'level',
        'duration_weeks',
        'hours_per_week',
        'description',
        'questionnaire_data',
        'ai_raw_response',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'questionnaire_data' => 'array',
            'ai_raw_response' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(PlanWeek::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(LearningSession::class);
    }
}
