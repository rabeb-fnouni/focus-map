<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Step;
use Illuminate\Http\Request;

class StepApiController extends Controller
{
    public function store(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'order' => 'nullable|integer',
        ]);

        $order = $request->input('order') ?? $goal->steps()->count() + 1;
        $validatedData['order'] = $order;

        $step = $goal->steps()->create($validatedData);
        
        // Update parent goal progress
        $goal->progress = $goal->calculateProgress();
        $goal->save();

        return response()->json($step);
    }
    
    public function update(Request $request, Step $step)
    {
        $this->authorize('update', $step->goal);
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
            'completed' => 'nullable|boolean',
        ]);

        $step->update($validatedData);

        // Update parent goal progress
        $goal = $step->goal;
        $goal->progress = $goal->calculateProgress();
        $goal->save();

        return response()->json($step);
    }
    
    public function destroy(Step $step)
    {
        $this->authorize('update', $step->goal);
        
        $goal = $step->goal;
        $step->delete();

        // Reorder remaining steps
        $remainingSteps = $goal->steps()->orderBy('order')->get();
        foreach ($remainingSteps as $index => $remainingStep) {
            $remainingStep->update(['order' => $index + 1]);
        }

        // Update parent goal progress
        $goal->progress = $goal->calculateProgress();
        $goal->save();

        return response()->json(['success' => true]);
    }
    
    public function reorder(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validatedData = $request->validate([
            'steps' => 'required|array',
            'steps.*' => 'integer|exists:steps,id',
        ]);

        foreach ($validatedData['steps'] as $index => $stepId) {
            $step = Step::find($stepId);
            if ($step && $step->goal_id === $goal->id) {
                $step->update(['order' => $index + 1]);
            }
        }

        return response()->json(['success' => true]);
    }
    
    public function toggleComplete(Step $step)
    {
        $this->authorize('update', $step->goal);
        
        $step->completed = !$step->completed;
        $step->save();

        // Update parent goal progress
        $goal = $step->goal;
        $goal->progress = $goal->calculateProgress();
        $goal->save();

        return response()->json([
            'success' => true,
            'completed' => $step->completed,
            'goalProgress' => $goal->progress
        ]);
    }
}