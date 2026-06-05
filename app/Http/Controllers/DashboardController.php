<?php

namespace App\Http\Controllers;

use App\Models\StudyPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $activePlans = StudyPlan::where('user_id', $user->id)
            ->withCount(['weeks', 'sessions'])
            ->whereIn('status', ['active', 'generating'])
            ->latest()
            ->get();

        $completedTopics = $user->studyPlans()
            ->with('weeks.topics')
            ->get()
            ->pluck('weeks')
            ->flatten()
            ->pluck('topics')
            ->flatten()
            ->where('is_completed', true)
            ->count();

        $totalTopics = $user->studyPlans()
            ->with('weeks.topics')
            ->get()
            ->pluck('weeks')
            ->flatten()
            ->pluck('topics')
            ->flatten()
            ->count();

        $totalStudyMinutes = $user->learningSessions()
            ->sum('duration_minutes');

        $recentSessions = $user->learningSessions()
            ->with(['studyPlan', 'topic'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'activePlans' => $activePlans,
            'stats' => [
                'completedTopics' => $completedTopics,
                'totalTopics' => $totalTopics,
                'totalStudyMinutes' => $totalStudyMinutes,
                'activePlansCount' => $activePlans->count(),
            ],
            'recentSessions' => $recentSessions,
        ]);
    }
}
