@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">👋 Welcome back, {{ Auth::user()->name }}!</h1>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-check2-circle"></i> Completed Goals</h5>
                    <p class="card-text fs-4">{{ $completedGoals }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-flag"></i> Total Goals</h5>
                    <p class="card-text fs-4">{{ $totalGoals }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-bar-chart-line"></i> Completion Rate</h5>
                    <p class="card-text fs-4">{{ round($completionRate) }}%</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="card-title">📈 Progress Overview</h4>
            <div class="progress mt-3">
                <div class="progress-bar progress-bar-striped bg-success" 
                     role="progressbar" 
                     style="width: {{ $completionRate }}%;" 
                     aria-valuenow="{{ $completionRate }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                     {{ round($completionRate) }}%
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Goals --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="card-title">🕒 Recent Goals</h4>
            @if($recentGoals->isEmpty())
                <p class="text-muted">No recent goals found.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($recentGoals as $goal)
                        <li class="list-group-item">
                            <strong>{{ $goal->title }}</strong><br>
                            <small class="text-muted">Started on: {{ $goal->start_date->format('d M Y') }}</small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Upcoming Goals --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="card-title">📅 Upcoming Goals</h4>
            @if($upcomingGoals->isEmpty())
                <p class="text-muted">No upcoming goals.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($upcomingGoals as $goal)
                        <li class="list-group-item">
                            <strong>{{ $goal->title }}</strong><br>
                            <small class="text-muted">Ends on: {{ $goal->end_date->format('d M Y') }}</small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Category Breakdown --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="card-title">📚 Goals by Category</h4>
            @if($categoryBreakdown->isEmpty())
                <p class="text-muted">No goals categorized yet.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($categoryBreakdown as $category)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $category->category ?? 'Uncategorized' }}
                            <span class="badge bg-primary rounded-pill">{{ $category->category_count }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

</div>
@endsection
