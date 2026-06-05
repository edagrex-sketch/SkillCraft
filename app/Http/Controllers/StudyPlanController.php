<?php

namespace App\Http\Controllers;

use App\Models\LearningSession;
use App\Models\PlanTopic;
use App\Models\StudyPlan;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudyPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->studyPlans()
            ->with(['weeks.topics']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('skill', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        $plans = $query->latest()->get()->map(function ($plan) {
            $topicsCount = $plan->weeks->sum(fn($w) => $w->topics->count());
            $completedCount = $plan->weeks->sum(fn($w) => $w->topics->where('is_completed', true)->count());
            $plan->topics_count = $topicsCount;
            $plan->completed_topics_count = $completedCount;
            return $plan;
        });

        return Inertia::render('StudyPlans/Index', [
            'plans' => $plans,
            'filters' => $request->only(['search', 'status', 'level']),
        ]);
    }

    public function create()
    {
        return Inertia::render('StudyPlans/Create');
    }

    public function store(Request $request, GroqService $groq)
    {
        $data = $request->validate([
            'skill' => 'required|string|max:255',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'hours_per_week' => 'required|integer|min:1|max:168',
            'weeks' => 'required|integer|min:1|max:52',
            'goals' => 'required|string|max:1000',
            'learning_style' => 'required|string',
            'focus' => 'nullable|string|max:255',
        ]);

        $plan = $request->user()->studyPlans()->create([
            'title' => "Aprendiendo {$data['skill']}",
            'skill' => $data['skill'],
            'level' => $data['level'],
            'duration_weeks' => $data['weeks'],
            'hours_per_week' => $data['hours_per_week'],
            'description' => $data['goals'],
            'questionnaire_data' => $data,
            'status' => 'generating',
        ]);

        try {
            $aiResponse = $groq->generateStudyPlan($data);

            $plan->update([
                'title' => $aiResponse['title'] ?? $plan->title,
                'description' => $aiResponse['description'] ?? $plan->description,
                'duration_weeks' => $aiResponse['duration_weeks'] ?? $plan->duration_weeks,
                'hours_per_week' => $aiResponse['hours_per_week'] ?? $plan->hours_per_week,
                'ai_raw_response' => $aiResponse,
                'status' => 'active',
            ]);

            foreach ($aiResponse['weeks'] ?? [] as $weekData) {
                $week = $plan->weeks()->create([
                    'week_number' => $weekData['week_number'],
                    'title' => $weekData['title'],
                    'summary' => $weekData['summary'] ?? '',
                    'order' => $weekData['week_number'],
                ]);

                foreach ($weekData['topics'] ?? [] as $order => $topicData) {
                    $week->topics()->create([
                        'title' => $topicData['title'],
                        'content_type' => $topicData['content_type'] ?? 'exercise',
                        'theory_content' => $topicData['theory'] ?? '',
                        'practice_description' => $topicData['practice'] ?? '',
                        'examples' => $topicData['examples'] ?? '',
                        'difficulty' => $topicData['difficulty'] ?? $data['level'],
                        'estimated_minutes' => $topicData['estimated_minutes'] ?? 30,
                        'order' => $order + 1,
                    ]);
                }
            }
        } catch (\Exception $e) {
            $plan->update(['status' => 'failed']);
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('study-plans.show', $plan);
    }

    public function show(StudyPlan $studyPlan)
    {
        $this->authorize('view', $studyPlan);

        $studyPlan->load(['weeks.topics.sessions' => function ($q) {
            $q->where('user_id', auth()->id());
        }]);

        return Inertia::render('StudyPlans/Show', [
            'plan' => $studyPlan,
        ]);
    }

    public function edit(StudyPlan $studyPlan)
    {
        $this->authorize('update', $studyPlan);

        return Inertia::render('StudyPlans/Edit', [
            'plan' => $studyPlan,
        ]);
    }

    public function update(Request $request, StudyPlan $studyPlan)
    {
        $this->authorize('update', $studyPlan);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'hours_per_week' => 'nullable|integer|min:1|max:168',
        ]);

        $studyPlan->update($data);

        return redirect()->route('study-plans.show', $studyPlan)
            ->with('success', 'Plan actualizado correctamente');
    }

    public function destroy(StudyPlan $studyPlan)
    {
        $this->authorize('delete', $studyPlan);

        $studyPlan->delete();
        return redirect()->route('study-plans.index');
    }

    public function retry(Request $request, GroqService $groq, StudyPlan $studyPlan)
    {
        $this->authorize('retry', $studyPlan);

        $data = $studyPlan->questionnaire_data;

        if (!$data) {
            return redirect()->back()->withErrors(['error' => 'No hay datos para regenerar el plan']);
        }

        $studyPlan->update(['status' => 'generating']);
        $studyPlan->weeks()->delete();

        try {
            $aiResponse = $groq->generateStudyPlan($data);

            $studyPlan->update([
                'title' => $aiResponse['title'] ?? $studyPlan->title,
                'description' => $aiResponse['description'] ?? $studyPlan->description,
                'duration_weeks' => $aiResponse['duration_weeks'] ?? $studyPlan->duration_weeks,
                'hours_per_week' => $aiResponse['hours_per_week'] ?? $studyPlan->hours_per_week,
                'ai_raw_response' => $aiResponse,
                'status' => 'active',
            ]);

            foreach ($aiResponse['weeks'] ?? [] as $weekData) {
                $week = $studyPlan->weeks()->create([
                    'week_number' => $weekData['week_number'],
                    'title' => $weekData['title'],
                    'summary' => $weekData['summary'] ?? '',
                    'order' => $weekData['week_number'],
                ]);

                foreach ($weekData['topics'] ?? [] as $order => $topicData) {
                    $week->topics()->create([
                        'title' => $topicData['title'],
                        'content_type' => $topicData['content_type'] ?? 'exercise',
                        'theory_content' => $topicData['theory'] ?? '',
                        'practice_description' => $topicData['practice'] ?? '',
                        'examples' => $topicData['examples'] ?? '',
                        'difficulty' => $topicData['difficulty'] ?? $studyPlan->level,
                        'estimated_minutes' => $topicData['estimated_minutes'] ?? 30,
                        'order' => $order + 1,
                    ]);
                }
            }
        } catch (\Exception $e) {
            $studyPlan->update(['status' => 'failed']);
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('study-plans.show', $studyPlan);
    }

    public function completeTopic(Request $request, PlanTopic $topic)
    {
        $studyPlan = $topic->week->studyPlan;
        $this->authorize('view', $studyPlan);

        $topic->update([
            'is_completed' => !$topic->is_completed,
            'completed_at' => $topic->is_completed ? null : now(),
        ]);

        return redirect()->back();
    }

    public function storeSession(Request $request, PlanTopic $topic)
    {
        $studyPlan = $topic->week->studyPlan;
        $this->authorize('createSession', $studyPlan);

        $data = $request->validate([
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'confidence_level' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:2000',
        ]);

        $session = $topic->sessions()->create([
            'user_id' => $request->user()->id,
            'study_plan_id' => $studyPlan->id,
            'duration_minutes' => $data['duration_minutes'],
            'confidence_level' => $data['confidence_level'],
            'notes' => $data['notes'] ?? '',
            'started_at' => now()->subMinutes($data['duration_minutes']),
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Sesion registrada');
    }

    public function destroySession(LearningSession $session)
    {
        $this->authorize('createSession', $session->studyPlan);

        $session->delete();
        return redirect()->back();
    }
}
