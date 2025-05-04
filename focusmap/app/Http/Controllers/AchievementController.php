<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $achievements = \App\Models\Achievement::all();
        return view('achievements.index', compact('achievements'));
        return view('achievements.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { $goals = auth()->user()->goals; // Gets all goals for the authenticated user
        return view('achievements.create', compact('goals'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $achievement = \App\Models\Achievement::findOrFail($id);
        return view('achievements.show', compact('achievement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $achievement = \App\Models\Achievement::findOrFail($id);
        return view('achievements.edit', compact('achievement'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'goal_id' => 'required|exists:goals,id',
        'badge_icon' => 'required|string',
        'achievement_type' => 'required|string',
        'threshold' => 'required|integer|min:1'
    ]);

    $achievement = \App\Models\Achievement::findOrFail($id);
    $achievement->update([
        'title' => $request->title,
        'description' => $request->description,
        'goal_id' => $request->goal_id,
        'badge_icon' => $request->badge_icon,
        'achievement_type' => $request->achievement_type,
        'threshold' => $request->threshold
    ]);

    return redirect()->route('achievements.index')->with('success', 'Achievement updated successfully.');
}
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $achievement = \App\Models\Achievement::findOrFail($id);
        $achievement->delete();

        return redirect()->route('achievements.index')->with('success', 'Achievement deleted successfully.');
    }
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'badge_icon' => 'required|string',
        'achievement_type' => 'required|string',
        'threshold' => 'required|integer|min:1',
        'goal_id' => 'nullable|exists:goals,id'
    ]);

    // Create the achievement with all fields
    $achievement = \App\Models\Achievement::create($validated);

    return redirect()->route('achievements.index')
        ->with('success', 'Achievement created successfully!');
}

}
