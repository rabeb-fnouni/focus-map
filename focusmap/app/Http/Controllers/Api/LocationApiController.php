<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    public function store(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $location = $goal->locations()->create($validatedData);

        return response()->json($location);
    }
    
    public function update(Request $request, Location $location)
    {
        $this->authorize('update', $location->goal);
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $location->update($validatedData);

        return response()->json($location);
    }
    
    public function destroy(Location $location)
    {
        $this->authorize('update', $location->goal);
        
        $location->delete();

        return response()->json(['success' => true]);
    }
}