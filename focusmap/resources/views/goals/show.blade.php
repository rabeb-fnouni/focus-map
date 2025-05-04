@extends('layouts.app')

@section('title', $goal->title)

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .goal-header {
        padding: 2rem 0;
        background-color: #f8f9fa;
        margin-bottom: 2rem;
    }
    
    .goal-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .goal-image-placeholder {
        width: 100%;
        height: 300px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .goal-image-placeholder i {
        font-size: 4rem;
        color: #dee2e6;
    }
    
    .goal-meta {
        margin-bottom: 1.5rem;
    }
    
    .goal-meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .goal-meta-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    
    .goal-meta-icon i {
        font-size: 1.2rem;
        color: #6c757d;
    }
    
    .priority-high .goal-meta-icon {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .priority-high .goal-meta-icon i {
        color: #dc3545;
    }
    
    .priority-medium .goal-meta-icon {
        background-color: rgba(253, 126, 20, 0.1);
    }
    
    .priority-medium .goal-meta-icon i {
        color: #fd7e14;
    }
    
    .priority-low .goal-meta-icon {
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    .priority-low .goal-meta-icon i {
        color: #0dcaf0;
    }
    
    .category-badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
    }
    
    .category-studies { background-color: #6f42c1; }
    .category-sports { background-color: #20c997; }
    .category-reading { background-color: #0dcaf0; }
    .category-projects { background-color: #fd7e14; }
    .category-health { background-color: #198754; }
    .category-other { background-color: #6c757d; }
    
    .progress-indicator {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .progress-indicator .progress {
        flex-grow: 1;
        height: 8px;
        margin-right: 1rem;
    }
    
    .goal-action-buttons {
        margin-top: 2rem;
    }
    
    .completion-toggle {
        margin-top: 1rem;
    }
    
    #locationMap {
        height: 300px;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .goal-description {
        white-space: pre-line;
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
<div class="goal-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge category-{{ $goal->category }} category-badge me-2">
                        {{ ucfirst($goal->category) }}
                    </span>
                    
                    @if($goal->completed)
                        <span class="badge bg-success">Completed</span>
                    @elseif($goal->end_date && $goal->end_date->isPast())
                        <span class="badge bg-danger">Overdue</span>
                    @elseif($goal->end_date && $goal->end_date->diffInDays(now()) <= 3)
                        <span class="badge bg-warning">Due Soon</span>
                    @else
                        <span class="badge bg-primary">In Progress</span>
                    @endif
                    
                    @if($goal->public)
                        <span class="badge bg-info ms-2">Public</span>
                    @else
                        <span class="badge bg-secondary ms-2">Private</span>
                    @endif
                </div>
                <h1 class="display-5 fw-bold">{{ $goal->title }}</h1>
            </div>
            <div>
                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Goals
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            @if($goal->image)
                <img src="{{ asset('storage/' . $goal->image) }}" class="goal-image" alt="{{ $goal->title }}">
            @else
                <div class="goal-image-placeholder">
                    <i class="bi bi-flag"></i>
                </div>
            @endif
            
            <div class="progress-indicator">
                <div class="progress">
                    <div class="progress-bar {{ $goal->completed ? 'bg-success' : '' }}" 
                         role="progressbar" 
                         style="width: {{ $goal->progress }}%;" 
                         aria-valuenow="{{ $goal->progress }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
                <div class="fw-bold">{{ $goal->progress }}%</div>
            </div>
            
            <div class="goal-description">
                <h2 class="h4 mb-3">Description</h2>
                {{ $goal->description }}
            </div>
            
            <div class="goal-action-buttons d-flex flex-wrap">
                <a href="{{ route('goals.edit', $goal) }}" class="btn btn-primary me-2 mb-2">
                    <i class="bi bi-pencil me-1"></i> Edit Goal
                </a>
                
                <button type="button" class="btn btn-outline-danger me-2 mb-2" data-bs-toggle="modal" data-bs-target="#deleteGoalModal">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
                
                <button type="button" class="btn btn-outline-secondary mb-2" id="updateProgressBtn">
                    <i class="bi bi-arrow-up-circle me-1"></i> Update Progress
                </button>
                
                <form action="{{ route('goals.toggleCompletion', $goal) }}" method="POST" class="ms-auto">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $goal->completed ? 'btn-outline-primary' : 'btn-success' }} mb-2">
                        @if($goal->completed)
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Mark as In Progress
                        @else
                            <i class="bi bi-check-circle me-1"></i> Mark as Complete
                        @endif
                    </button>
                </form>
            </div>
            
            <div id="progressUpdateForm" class="mt-3" style="display: none;">
                <form action="{{ route('goals.updateProgress', $goal) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h5 mb-3">Update Progress</h3>
                            <div class="mb-3">
                                <label for="progress" class="form-label">Progress Percentage</label>
                                <input type="range" class="form-range" id="progress" name="progress" 
                                       min="0" max="100" step="5" value="{{ $goal->progress }}">
                                <div class="d-flex justify-content-between">
                                    <small>0%</small>
                                    <small>50%</small>
                                    <small>100%</small>
                                </div>
                                <div class="text-center mt-2">
                                    <span class="badge bg-primary" id="progressValue">{{ $goal->progress }}%</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary me-2" id="cancelProgressUpdate">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Progress</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Goal Details</h2>
                    <div class="goal-meta">
                        <div class="goal-meta-item priority-{{ $goal->priority }}">
                            <div class="goal-meta-icon">
                                <i class="bi bi-flag"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Priority</div>
                                <div>{{ ucfirst($goal->priority) }}</div>
                            </div>
                        </div>
                        
                        <div class="goal-meta-item">
                            <div class="goal-meta-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Start Date</div>
                                <div>{{ $goal->start_date ? $goal->start_date->format('M d, Y') : 'Not specified' }}</div>
                            </div>
                        </div>
                        
                        <div class="goal-meta-item">
                            <div class="goal-meta-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Deadline</div>
                                <div>{{ $goal->end_date ? $goal->end_date->format('M d, Y') : 'Not specified' }}</div>
                            </div>
                        </div>
                        
                        <div class="goal-meta-item">
                            <div class="goal-meta-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Created</div>
                                <div>{{ $goal->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($goal->latitude && $goal->longitude)
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Location</h2>
                        <div id="locationMap"></div>
                        <div class="mt-2">
                            <div class="fw-bold">{{ $goal->location_name ?: 'Goal Location' }}</div>
                            <div class="small text-muted">
                                Lat: {{ $goal->latitude }}, Long: {{ $goal->longitude }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Goal Modal -->
<div class="modal fade" id="deleteGoalModal" tabindex="-1" aria-labelledby="deleteGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGoalModalLabel">Delete Goal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this goal? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> All information related to this goal will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('goals.destroy', $goal) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Goal</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Progress slider
        const progressInput = document.getElementById('progress');
        const progressValue = document.getElementById('progressValue');
        
        if (progressInput && progressValue) {
            progressInput.addEventListener('input', function() {
                progressValue.textContent = this.value + '%';
            });
        }
        
        // Toggle progress update form
        const updateProgressBtn = document.getElementById('updateProgressBtn');
        const progressUpdateForm = document.getElementById('progressUpdateForm');
        const cancelProgressUpdate = document.getElementById('cancelProgressUpdate');
        
        if (updateProgressBtn && progressUpdateForm && cancelProgressUpdate) {
            updateProgressBtn.addEventListener('click', function() {
                progressUpdateForm.style.display = 'block';
                updateProgressBtn.style.display = 'none';
            });
            
            cancelProgressUpdate.addEventListener('click', function() {
                progressUpdateForm.style.display = 'none';
                updateProgressBtn.style.display = 'inline-block';
            });
        }
        
        // Initialize map if location exists
        @if($goal->latitude && $goal->longitude)
            function initializeMap() {
                const map = L.map('locationMap').setView([{{ $goal->latitude }}, {{ $goal->longitude }}], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                L.marker([{{ $goal->latitude }}, {{ $goal->longitude }}])
                    .addTo(map)
                    .bindPopup("{{ $goal->location_name ?: 'Goal Location' }}")
                    .openPopup();
            }
            
            initializeMap();
        @endif
    });
</script>
@endsection