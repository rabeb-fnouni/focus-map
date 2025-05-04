@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Goals Timeline</h1>
                    <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filters and sorting -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('goals.timeline') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select name="category" id="category" class="form-select">
                                        <option value="all">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="all">All Statuses</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="sort" class="form-label">Sort By</label>
                                    <select name="sort" id="sort" class="form-select">
                                        <option value="created_at_desc" {{ $sort == 'created_at_desc' ? 'selected' : '' }}>Creation Date (Newest)</option>
                                        <option value="created_at_asc" {{ $sort == 'created_at_asc' ? 'selected' : '' }}>Creation Date (Oldest)</option>
                                        <option value="deadline_asc" {{ $sort == 'deadline_asc' ? 'selected' : '' }}>Deadline (Soonest)</option>
                                        <option value="deadline_desc" {{ $sort == 'deadline_desc' ? 'selected' : '' }}>Deadline (Latest)</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    @if(request()->hasAny(['category', 'status', 'sort']))
                                        <a href="{{ route('goals.timeline') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="timeline">
                        @forelse($goals as $goal)
                        <div class="timeline-item">
                            <div class="timeline-date">
                                {{ $goal->created_at->format('d M Y') }}
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h5>{{ $goal->title }}</h5>
                                    <span class="badge bg-{{ $goal->progress == 100 ? 'success' : 'primary' }}">
                                        {{ $goal->progress }}%
                                    </span>
                                </div>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-tag-fill"></i> {{ ucfirst($goal->category) }}
                                </p>
                                <p>{{ $goal->description }}</p>
                                <div class="timeline-progress">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped {{ $goal->progress == 100 ? 'bg-success' : '' }}" 
                                             role="progressbar" 
                                             style="width: {{ $goal->progress }}%" 
                                             aria-valuenow="{{ $goal->progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                @if($goal->deadline)
                                <div class="timeline-deadline mt-2">
                                    <i class="bi bi-calendar-check"></i> 
                                    Deadline: {{ $goal->deadline->format('d M Y') }}
                                    @if($goal->deadline->isPast() && $goal->progress < 100)
                                        <span class="text-danger ms-2">
                                            <i class="bi bi-exclamation-triangle"></i> Overdue
                                        </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info">
                            No goals found with these filters.
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $goals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 50px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-date {
        position: absolute;
        left: -50px;
        width: 40px;
        text-align: right;
        font-size: 0.9rem;
        color: #6c757d;
    }
    .timeline-content {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #0d6efd;
    }
    .timeline-progress {
        margin: 10px 0;
    }
    .timeline-progress .progress {
        height: 8px;
        border-radius: 4px;
    }
    .timeline-progress .progress-bar {
        border-radius: 4px;
    }
    .timeline-deadline {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .pagination {
        justify-content: center;
    }
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
    }
</style>
@endsection