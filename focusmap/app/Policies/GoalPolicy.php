<?php
namespace App\Policies;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoalPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Goal $goal)
    {
        // Users can view their own goals
        if ($user->id === $goal->user_id) {
            return true;
        }
        
        // Users can view goals shared with them
        if ($goal->sharedUsers()->where('users.id', $user->id)->exists()) {
            return true;
        }
        
        // Users can view public goals
        if ($goal->visibility === 'public') {
            return true;
        }
        
        return false;
    }

    public function update(User $user, Goal $goal)
    {
        // Users can update their own goals
        if ($user->id === $goal->user_id) {
            return true;
        }
        
        // Users with edit or admin permission can update shared goals
        $sharedUser = $goal->sharedUsers()->where('users.id', $user->id)->first();
        if ($sharedUser && in_array($sharedUser->pivot->permission_level, ['edit', 'admin'])) {
            return true;
        }
        
        return false;
    }

    public function delete(User $user, Goal $goal)
    {
        // Users can delete their own goals
        if ($user->id === $goal->user_id) {
            return true;
        }
        
        // Users with admin permission can delete shared goals
        $sharedUser = $goal->sharedUsers()->where('users.id', $user->id)->first();
        if ($sharedUser && $sharedUser->pivot->permission_level === 'admin') {
            return true;
        }
        
        return false;
    }

    public function share(User $user, Goal $goal)
    {
        // Only the owner can share the goal
        return $user->id === $goal->user_id;
    }
}