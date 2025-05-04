<?php
namespace App\Http\Controllers;
use App\Models\Goal;
use App\Models\Step;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
class GoalController extends Controller
{
    public function index()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $allGoals = $user->goals()
        ->with(['steps', 'locations', 'journals'])
        ->latest()
        ->get();
        
    $activeGoals = $allGoals->where('completed', false);
    $completedGoals = $allGoals->where('completed', true);

    return view('goals.index', [
        'goals' => $allGoals, // This is the variable your view expects
        'activeGoals' => $activeGoals,
        'completedGoals' => $completedGoals,
        'allGoals' => $allGoals
    ]);
}

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'required|string',
        'priority' => 'required|in:high,medium,low',
        'start_date' => 'nullable|date',
        'end_date' => 'required|date',
        'progress' => 'nullable|integer|min:0|max:100',
        'location_name' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'image' => 'nullable|image|max:2048',
        'public' => 'nullable|boolean'
    ]);

    $goal = new Goal($request->all());
    $goal->user_id = auth()->id();
    
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('goal_images', 'public');
        $goal->image = $path;
    }
    
    $goal->save();

    return redirect()->route('goals.index')->with('success', 'Goal created successfully!');
}

    public function show(Goal $goal)
    {
        return view('goals.show', compact('goal'));
    }

    public function edit(Goal $goal)
    {
        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'due_date' => 'required|date'
        ]);

        $goal->update($request->only(['name', 'description', 'due_date']));
        return redirect()->route('goals.index')->with('success', 'Goal updated successfully!');
    }
    public function mindmap()
    {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    
    $goals = $user->goals()
        ->with('steps')
        ->latest()
        ->get();
    
    $sharedGoals = $user->sharedGoals()
        ->with('steps', 'user')
        ->latest()
        ->get();
    
    return view('goals.mindmap', compact('goals', 'sharedGoals'));
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();
        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully!');
    }
    public function showMap()
   {
    return view('map');
   }
   public function timeline(Request $request)
{
    $query = Goal::where('user_id', auth()->id());
    
    // Filtrage par catégorie
    if ($request->has('category') && $request->category != 'all') {
        $query->where('category', $request->category);
    }
    
    // Filtrage par statut
    if ($request->has('status')) {
        if ($request->status == 'completed') {
            $query->where('progress', 100);
        } elseif ($request->status == 'in_progress') {
            $query->where('progress', '<', 100);
        }
    }
    
    // Tri des résultats
    $sort = $request->get('sort', 'created_at_desc');
    switch ($sort) {
        case 'created_at_asc':
            $query->orderBy('created_at', 'asc');
            break;
        case 'deadline_asc':
            $query->orderBy('deadline', 'asc');
            break;
        case 'deadline_desc':
            $query->orderBy('deadline', 'desc');
            break;
        default: // created_at_desc
            $query->orderBy('created_at', 'desc');
    }
    
    // Pagination (10 éléments par page)
    $goals = $query->paginate(10)->appends($request->query());
    
    // Correction: Récupérer les catégories distinctes sans ORDER BY problématique
    $categories = Goal::where('user_id', auth()->id())
                    ->select('category')
                    ->distinct()
                    ->pluck('category');
    
    return view('goals.timeline', compact('goals', 'categories', 'sort'));
}
public function toggleCompletion($id)
{
    $goal = Goal::findOrFail($id);

    $goal->is_completed = !$goal->is_completed;
    $goal->save(); // This should NOT insert a new record

    return redirect()->back();
}


public function updateProgress(Request $request, Goal $goal)
{
    $validated = $request->validate([
        'progress' => 'required|numeric|min:0|max:100'
    ]);
    
    $goal->update(['progress' => $validated['progress']]);
    
    return back()->with('success', 'Progress updated!');
    
    // For AJAX requests, you might return JSON instead:
    // return response()->json(['success' => true]);
}
public function saveGoalWithSteps(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'steps' => 'required|array',
        'steps.*.title' => 'required|string|max:255',
        'steps.*.description' => 'nullable|string',
        'location' => 'nullable|array',
        'location.name' => 'required_with:location|string|max:255',
        'location.latitude' => 'required_with:location|numeric',
        'location.longitude' => 'required_with:location|numeric',
    ]);

    // Commencer une transaction pour s'assurer que tout est sauvegardé correctement
    DB::beginTransaction();

    try {
        // Créer l'objectif
        $goal = new Goal();
        $goal->user_id = auth()->id();
        $goal->title = $request->title;
        $goal->description = $request->description;
        $goal->progress = 0;
        $goal->deadline = now()->addMonth(); // Date limite par défaut à 1 mois
        
        $goal->save();

        // Enregistrer les étapes
        $stepsCount = count($request->steps);
        foreach ($request->steps as $index => $stepData) {
            $step = new Step();
            $step->goal_id = $goal->id;
            $step->title = $stepData['title'];
            $step->description = $stepData['description'] ?? null;
            $step->progress = 0;
            $step->order = $index;
            $step->deadline = now()->addDays(15 + $index); // Échelonner les dates limites
            $step->completed = false;
            
            $step->save();
        }

        // Enregistrer la localisation si elle existe
        if ($request->has('location')) {
            $location = new Location();
            $location->goal_id = $goal->id;
            $location->name = $request->location['name'];
            $location->latitude = $request->location['latitude'];
            $location->longitude = $request->location['longitude'];
            
            $location->save();
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Objectif et étapes sauvegardés avec succès',
            'goal_id' => $goal->id
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
        ], 500);
    }
}

}
