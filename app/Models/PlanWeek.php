<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanWeek extends Model
{
    protected $fillable = [
        'study_plan_id',
        'week_number',
        'title',
        'summary',
        'order',
    ];

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(PlanTopic::class);
    }
}
