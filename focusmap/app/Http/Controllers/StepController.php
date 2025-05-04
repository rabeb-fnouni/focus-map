<?php

namespace App\Http\Controllers;
use App\Models\Goal;
use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date'
        ]);

        Step::create($request->only(['title', 'description', 'due_date']));
        return redirect()->route('steps.index')->with('success', 'Step created successfully!');
    }

    public function update(Request $request, Step $step)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date'
        ]);

        $step->update($request->only(['title', 'description', 'due_date']));
        return redirect()->route('steps.index')->with('success', 'Step updated successfully!');
    }

    public function destroy(Step $step)
    {
        $step->delete();
        return redirect()->route('steps.index')->with('success', 'Step deleted successfully!');
    }
}
