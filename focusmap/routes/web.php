<?php
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SharingController;
use App\Http\Controllers\StepController;
use App\Http\Controllers\MindMapController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Goals
    Route::resource('goals', GoalController::class);
    Route::get('/mindmap', [MindMapController::class, 'index'])->name('mindmap.index')->middleware('auth');
    Route::get('/map', function () {
        return view('map');
    })->name('goals.map');
    Route::get('/timeline', [GoalController::class, 'timeline'])
     ->name('goals.timeline'); 
     Route::match(['post', 'patch'], '/goals/{id}/toggle-completion', [GoalController::class, 'toggleCompletion'])
     ->name('goals.toggleCompletion');

     Route::match(['PATCH', 'POST'], '/goals/{goal}/update-progress', [GoalController::class, 'updateProgress'])
     ->name('goals.updateProgress');
    // Steps
    Route::post('/goals/{goal}/steps', [StepController::class, 'store'])->name('steps.store');
    Route::put('/steps/{step}', [StepController::class, 'update'])->name('steps.update');
    Route::delete('/steps/{step}', [StepController::class, 'destroy'])->name('steps.destroy');
    Route::post('/goals/{goal}/steps/reorder', [StepController::class, 'reorder'])->name('steps.reorder');
    Route::post('/steps/{step}/toggle-complete', [StepController::class, 'toggleComplete'])->name('steps.toggle-complete');
    
    // Locations
    Route::post('/goals/{goal}/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    
    // Journals
    Route::post('/goals/{goal}/journals', [JournalController::class, 'store'])->name('journals.store');
    Route::put('/journals/{journal}', [JournalController::class, 'update'])->name('journals.update');
    Route::delete('/journals/{journal}', [JournalController::class, 'destroy'])->name('journals.destroy');
    
    // Sharing
    Route::post('/goals/{goal}/share', [SharingController::class, 'share'])->name('goals.share');
    Route::put('/goals/{goal}/share/{user}', [SharingController::class, 'updatePermission'])->name('goals.share.update');
    Route::delete('/goals/{goal}/share/{user}', [SharingController::class, 'unshare'])->name('goals.share.destroy');
    
    // Achievements
    Route::resource('achievements', AchievementController::class);
   
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
