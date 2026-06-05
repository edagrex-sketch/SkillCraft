<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    public function show()
    {
        return Inertia::render('Profile/Onboarding');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'learning_style' => 'required|string|in:visual,auditivo,lectura,kinestesico,practico',
            'time_availability_minutes' => 'required|integer|min:30|max:1440',
            'goals' => 'required|string|max:1000',
            'experience_level' => 'required|string|in:beginner,intermediate,advanced',
        ]);

        $request->user()->update($data);

        return redirect()->route('dashboard');
    }
}
