<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// routes/api.php
use App\Http\Controllers\Api\AiSuggestionController;
use App\Http\Controllers\Api\GoalApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\StepApiController;
use App\Http\Controllers\MindMapController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Goals API
    Route::get('/goals', [GoalApiController::class, 'index']);
    Route::get('/goals/{goal}', [GoalApiController::class, 'show']);
    Route::post('/goals', [GoalApiController::class, 'store']);
    Route::put('/goals/{goal}', [GoalApiController::class, 'update']);
    Route::delete('/goals/{goal}', [GoalApiController::class, 'destroy']);
    Route::get('/mindmap-data', [GoalApiController::class, 'mindmapData']);
    Route::get('/map-data', [GoalApiController::class, 'mapData']);
    Route::get('/timeline-data', [GoalApiController::class, 'timelineData']);
    
    // Steps API
    Route::post('/goals/{goal}/steps', [StepApiController::class, 'store']);
    Route::put('/steps/{step}', [StepApiController::class, 'update']);
    Route::delete('/steps/{step}', [StepApiController::class, 'destroy']);
    Route::post('/goals/{goal}/steps/reorder', [StepApiController::class, 'reorder']);
    Route::post('/steps/{step}/toggle-complete', [StepApiController::class, 'toggleComplete']);
    
    // Locations API
    Route::post('/goals/{goal}/locations', [LocationApiController::class, 'store']);
    Route::put('/locations/{location}', [LocationApiController::class, 'update']);
    Route::delete('/locations/{location}', [LocationApiController::class, 'destroy']);
    
    // AI Suggestions API
    Route::post('/goals/{goal}/generate-steps', [AiSuggestionController::class, 'generateSteps']);
    
    // MindMap API
    Route::middleware('auth:api')->group(function () {
    Route::post('/generate-mindmap', [MindMapController::class, 'generateMindMap']);
    Route::post('/save-goal-with-steps', [MindMapController::class, 'saveGoalWithSteps']);
    Route::get('/goals', [MindMapController::class, 'getGoals']);
});
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

