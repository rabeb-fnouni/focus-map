<!-- resources/views/achievements/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white;">
                    <div class="d-flex align-items-center">
                        <i class="{{ $achievement->badge_icon }} fa-2x me-3"></i>
                        <h3 class="mb-0">{{ $achievement->name }}</h3>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('achievements.edit', $achievement->id) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-edit text-primary"></i>
                        </a>
                        <form action="{{ route('achievements.destroy', $achievement->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light ms-1" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="display-1">
                                <i class="{{ $achievement->badge_icon }}" 
                                   style="color: 
                                       @if($achievement->achievement_type == 'completion') #FFD700 
                                       @elseif($achievement->achievement_type == 'streak') #C0C0C0 
                                       @elseif($achievement->achievement_type == 'milestone') #CD7F32 
                                       @else #6A5ACD @endif">
                                </i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h5 class="text-muted">Achievement Details</h5>
                            <p class="lead">{{ $achievement->description }}</p>
                            
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-tag me-1"></i> {{ ucfirst($achievement->achievement_type) }}
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-bullseye me-1"></i> Threshold: {{ $achievement->threshold }}
                                </span>
                            </div>

                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 75%;" aria-valuenow="75" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    75% Complete
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <h5><i class="fas fa-history me-2"></i>Recent Earners</h5>
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=John+Doe" alt="John Doe" class="rounded-circle me-3" width="40">
                                    <div>
                                        <h6 class="mb-0">John Doe</h6>
                                        <small class="text-muted">Earned 2 days ago</small>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Jane+Smith" alt="Jane Smith" class="rounded-circle me-3" width="40">
                                    <div>
                                        <h6 class="mb-0">Jane Smith</h6>
                                        <small class="text-muted">Earned 1 week ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('achievements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Achievements
                    </a>
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i> 
                            Created: {{ $achievement->created_at->format('M d, Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        border-radius: 10px;
    }
    .list-group-item {
        border-left: 0;
        border-right: 0;
    }
    .list-group-item:first-child {
        border-top: 0;
    }
</style>
@endsection