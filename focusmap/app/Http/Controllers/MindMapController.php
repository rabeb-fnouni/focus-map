<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Goal;

class MindMapController extends Controller
{
    /**
     * Afficher la page de mind map
     */
    public function index()
    {
        return view('mindmap.index');
    }

    /**
     * Générer les données de la mind map et géocoder la localisation
     */
    public function generateMindMap(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'steps' => 'array',
            'steps.*.title' => 'required|string',
            'steps.*.category' => 'required|string',
            'location_name' => 'nullable|string',
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $steps = $request->input('steps', []);
        $locationName = $request->input('location_name');

        $data = [
            'title' => $title,
            'description' => $description,
            'steps' => array_map(function ($step) {
                return [
                    'title' => $step['title'],
                    'category' => $step['category'],
                    'tasks' => [$step['title']] // Treat the step title as a task for rendering
                ];
            }, $steps),
            'location' => null,
        ];

        // Geocode location using Nominatim if provided
        if ($locationName) {
            try {
                $geocodeResponse = Http::withHeaders([
                    'User-Agent' => config('services.nominatim.user_agent', 'MindMapApp/1.0 (contact: your.email@example.com)'),
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $locationName,
                    'format' => 'json',
                    'limit' => 1,
                ]);

                $geocodeData = $geocodeResponse->json();
                Log::info('Nominatim response: ' . json_encode($geocodeData));

                if (!empty($geocodeData) && isset($geocodeData[0]['lat'], $geocodeData[0]['lon'])) {
                    $data['location'] = [
                        'name' => $locationName,
                        'lat' => (float) $geocodeData[0]['lat'],
                        'lng' => (float) $geocodeData[0]['lon'],
                    ];
                } else {
                    Log::warning('Nominatim failed to geocode: ' . $locationName);
                }
            } catch (\Exception $e) {
                Log::error('Nominatim geocoding error: ' . $e->getMessage());
            }
        }

        return response()->json($data);
    }

    /**
     * Sauvegarder l'objectif et ses étapes
     */
    public function saveGoalWithSteps(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'steps' => 'array',
            'steps.*.title' => 'required|string',
            'steps.*.category' => 'required|string',
            'location' => 'nullable|array',
            'location.name' => 'nullable|string',
            'location.lat' => 'nullable|numeric|between:-90,90',
            'location.lng' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $goal = Goal::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'category' => 'Manual',
                'progress' => 0,
                'completed' => false,
                'start_date' => now(),
                'end_date' => null,
                'visibility' => 'private',
                'location_name' => $request->location['name'] ?? null,
                'latitude' => $request->location['lat'] ?? null,
                'longitude' => $request->location['lng'] ?? null,
            ]);

            foreach ($request->steps as $step) {
                $goal->steps()->create([
                    'title' => $step['title'],
                    'category' => $step['category'],
                    'description' => null,
                    'progress' => 0,
                    'deadline' => null,
                    'completed' => false,
                    'order' => 0,
                ]);
            }

            return response()->json([
                'message' => 'Goal and steps saved successfully',
                'goal' => $goal->load('steps')
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving goal: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save goal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Récupérer les objectifs existants de l'utilisateur
     */
    public function getGoals()
    {
        $goals = Goal::where('user_id', auth()->id())->with('steps')->get();
        Log::info('Fetched goals: ' . json_encode($goals));
        return response()->json($goals);
    }
}