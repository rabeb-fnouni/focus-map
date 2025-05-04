<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalApiController extends Controller
{
    public function index()
    {
        $goals = Auth::user()->goals()->with('steps', 'locations')->get();
        $sharedGoals = Auth::user()->sharedGoals()->with('steps', 'locations', 'user')->get();
        
        return response()->json([
            'goals' => $goals,
            'sharedGoals' => $sharedGoals
        ]);
    }
    
    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        
        $goal->load('steps', 'locations', 'journals.user', 'sharedUsers');
        
        return response()->json($goal);
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'visibility' => 'required|in:private,shared,public',
        ]);
    
        $goal = Auth::user()->goals()->create($validatedData);

        if ($request->has('location')) {
            $goal->locations()->create([
                'title' => $request->input('location_title'),
                'description' => $request->input('location_description'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);
        }

        return response()->json($goal->load('steps', 'locations'));
    }
    
    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'visibility' => 'required|in:private,shared,public',
        ]);

        $goal->update($validatedData);

        return response()->json($goal->load('steps', 'locations'));
    }
    
    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        
        $goal->delete();

        return response()->json(['success' => true]);
    }
    
    public function mindmapData()
    {
        $goals = Auth::user()->goals()->with('steps')->get();
        $sharedGoals = Auth::user()->sharedGoals()->with('steps', 'user')->get();
        
        $mindmapData = [
            'id' => 'root',
            'name' => Auth::user()->name . "'s Goals",
            'children' => []
        ];
        
        // Format own goals
        foreach ($goals as $goal) {
            $goalNode = [
                'id' => 'goal_' . $goal->id,
                'name' => $goal->title,
                'category' => $goal->category,
                'progress' => $goal->progress,
                'children' => []
            ];
            
            foreach ($goal->steps as $step) {
                $goalNode['children'][] = [
                    'id' => 'step_' . $step->id,
                    'name' => $step->title,
                    'progress' => $step->progress,
                    'completed' => $step->completed
                ];
            }
            
            $mindmapData['children'][] = $goalNode;
        }
        
        // Format shared goals
        if ($sharedGoals->count() > 0) {
            $sharedNode = [
                'id' => 'shared',
                'name' => 'Shared Goals',
                'children' => []
            ];
            
            foreach ($sharedGoals as $goal) {
                $goalNode = [
                    'id' => 'shared_goal_' . $goal->id,
                    'name' => $goal->title . ' (' . $goal->user->name . ')',
                    'category' => $goal->category,
                    'progress' => $goal->progress,
                    'children' => []
                ];
                
                foreach ($goal->steps as $step) {
                    $goalNode['children'][] = [
                        'id' => 'shared_step_' . $step->id,
                        'name' => $step->title,
                        'progress' => $step->progress,
                        'completed' => $step->completed
                    ];
                }
                
                $sharedNode['children'][] = $goalNode;
            }
            
            $mindmapData['children'][] = $sharedNode;
        }
        
        return response()->json($mindmapData);
    }
    
    public function mapData()
    {
        $goals = Auth::user()->goals()->with('locations')->get();
        $sharedGoals = Auth::user()->sharedGoals()->with('locations', 'user')->get();
        
        $mapData = [];
        
        // Format own goals with locations
        foreach ($goals as $goal) {
            foreach ($goal->locations as $location) {
                $mapData[] = [
                    'id' => 'location_' . $location->id,
                    'goalId' => $goal->id,
                    'title' => $goal->title,
                    'locationTitle' => $location->title,
                    'description' => $location->description,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'category' => $goal->category,
                    'progress' => $goal->progress,
                    'isShared' => false,
                ];
            }
        }
        
        // Format shared goals with locations
        foreach ($sharedGoals as $goal) {
            foreach ($goal->locations as $location) {
                $mapData[] = [
                    'id' => 'shared_location_' . $location->id,
                    'goalId' => $goal->id,
                    'title' => $goal->title . ' (' . $goal->user->name . ')',
                    'locationTitle' => $location->title,
                    'description' => $location->description,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'category' => $goal->category,
                    'progress' => $goal->progress,
                    'isShared' => true,
                ];
            }
        }
        
        return response()->json($mapData);
    }
    
    public function timelineData()
    {
        $goals = Auth::user()->goals()->with('steps')->get();
        $sharedGoals = Auth::user()->sharedGoals()->with('steps', 'user')->get();
        
        $timelineData = [];
        
        // Format own goals for timeline
        foreach ($goals as $goal) {
            $timelineData[] = [
                'id' => 'goal_' . $goal->id,
                'type' => 'goal',
                'title' => $goal->title,
                'category' => $goal->category,
                'startDate' => $goal->start_date->format('Y-m-d'),
                'endDate' => $goal->end_date ? $goal->end_date->format('Y-m-d') : null,
                'progress' => $goal->progress,
                'isShared' => false,
            ];
            
            foreach ($goal->steps as $step) {
                if ($step->deadline) {
                    $timelineData[] = [
                        'id' => 'step_' . $step->id,
                        'type' => 'step',
                        'goalId' => $goal->id,
                        'title' => $step->title,
                        'date' => $step->deadline->format('Y-m-d'),
                        'completed' => $step->completed,
                        'isShared' => false,
                    ];
                }
            }
        }
        
        // Format shared goals for timeline
        foreach ($sharedGoals as $goal) {
            $timelineData[] = [
                'id' => 'shared_goal_' . $goal->id,
                'type' => 'goal',
                'title' => $goal->title . ' (' . $goal->user->name . ')',
                'category' => $goal->category,
                'startDate' => $goal->start_date->format('Y-m-d'),
                'endDate' => $goal->end_date ? $goal->end_date->format('Y-m-d') : null,
                'progress' => $goal->progress,
                'isShared' => true,
            ];
            
            foreach ($goal->steps as $step) {
                if ($step->deadline) {
                    $timelineData[] = [
                        'id' => 'shared_step_' . $step->id,
                        'type' => 'step',
                        'goalId' => $goal->id,
                        'title' => $step->title,
                        'date' => $step->deadline->format('Y-m-d'),
                        'completed' => $step->completed,
                        'isShared' => true,
                    ];
                }
            }
        }
        
        return response()->json($timelineData);
    }
}