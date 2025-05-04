<?php
namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SharingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function share(Request $request, Goal $goal)
    {
        $this->authorize('share', $goal);
        
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'permission_level' => 'required|in:view,edit,admin',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        // Don't share with yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot share a goal with yourself.');
        }

        // Check if already shared
        if ($goal->sharedUsers()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'This goal is already shared with this user.');
        }

        $goal->sharedUsers()->attach($user->id, [
            'permission_level' => $validatedData['permission_level']
        ]);

        return redirect()->route('goals.show', $goal)->with('success', 'Goal shared successfully!');
    }

    public function updatePermission(Request $request, Goal $goal, User $user)
    {
        $this->authorize('share', $goal);
        
        $validatedData = $request->validate([
            'permission_level' => 'required|in:view,edit,admin',
        ]);

        $goal->sharedUsers()->updateExistingPivot($user->id, [
            'permission_level' => $validatedData['permission_level']
        ]);

        return redirect()->route('goals.show', $goal)->with('success', 'Sharing permission updated successfully!');
    }

    public function unshare(Goal $goal, User $user)
    {
        $this->authorize('share', $goal);
        
        $goal->sharedUsers()->detach($user->id);

        return redirect()->route('goals.show', $goal)->with('success', 'Goal unshared successfully!');
    }
}
