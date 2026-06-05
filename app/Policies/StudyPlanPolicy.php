<?php

namespace App\Policies;

use App\Models\StudyPlan;
use App\Models\User;

class StudyPlanPolicy
{
    public function view(User $user, StudyPlan $studyPlan): bool
    {
        return $user->id === $studyPlan->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, StudyPlan $studyPlan): bool
    {
        return $user->id === $studyPlan->user_id;
    }

    public function delete(User $user, StudyPlan $studyPlan): bool
    {
        return $user->id === $studyPlan->user_id;
    }

    public function retry(User $user, StudyPlan $studyPlan): bool
    {
        return $user->id === $studyPlan->user_id && $studyPlan->status === 'failed';
    }

    public function createSession(User $user, StudyPlan $studyPlan): bool
    {
        return $user->id === $studyPlan->user_id;
    }
}
