@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-trophy text-primary me-2"></i>Achievements
                </h2>
                <a href="{{ route('achievements.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create New
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($achievements as $achievement)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="{{ $achievement->badge_icon }} fa-4x" 
                               style="color: 
                                   @if($achievement->achievement_type == 'completion') #FFD700 
                                   @elseif($achievement->achievement_type == 'streak') #C0C0C0 
                                   @elseif($achievement->achievement_type == 'milestone') #CD7F32 
                                   @else #6A5ACD @endif">
                            </i>
                        </div>
                        <h5 class="card-title">{{ $achievement->name }}</h5>
                        <p class="card-text text-muted">{{ $achievement->description }}</p>
                        <span class="badge bg-info text-dark mb-3">
                            {{ ucfirst($achievement->achievement_type) }} (Threshold: {{ $achievement->threshold }})
                        </span>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('achievements.show', $achievement->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('achievements.edit', $achievement->id) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('achievements.destroy', $achievement->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i> No achievements found. Create your first one!
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.5em 0.75em;
    }
</style>
@endsection