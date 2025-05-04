<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Goal;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Get recent goals (already started)
        $recentGoals = $user->goals()
            ->where('start_date', '<=', Carbon::today())
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get();
            
        // Get upcoming goals (future end dates)
        $upcomingGoals = $user->goals()
            ->where('end_date', '>=', Carbon::today())
            ->orderBy('end_date', 'asc')
            ->limit(5)
            ->get();
            
        // Get progress statistics
        $completedGoals = $user->goals()->where('progress', 100)->count();
        $totalGoals = $user->goals()->count();
        $completionRate = $totalGoals > 0 ? ($completedGoals / $totalGoals) * 100 : 0;
        
        $categoryBreakdown = Goal::where('user_id', auth()->id())
    ->groupBy('category')
    ->select('category', DB::raw('COUNT(*) AS category_count'), DB::raw('MAX(created_at) AS latest_created_at'))
    ->orderBy('latest_created_at', 'desc') // Only order by aggregated column
    ->get();
            
        return view('dashboard', compact(
            'recentGoals', 
            'upcomingGoals', 
            'completedGoals', 
            'totalGoals', 
            'completionRate', 
            'categoryBreakdown'
        ));
    }
}
