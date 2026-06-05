<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Profile\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyPlanController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store']);

    Route::resource('study-plans', StudyPlanController::class);

    Route::post('/study-plans/{study_plan}/retry', [StudyPlanController::class, 'retry'])->name('study-plans.retry');
    Route::patch('/topics/{topic}/complete', [StudyPlanController::class, 'completeTopic'])->name('topics.complete');
    Route::post('/topics/{topic}/sessions', [StudyPlanController::class, 'storeSession'])->name('topics.sessions.store');
    Route::delete('/sessions/{session}', [StudyPlanController::class, 'destroySession'])->name('sessions.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
