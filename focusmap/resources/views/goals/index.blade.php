@extends('layouts.app')

@section('title', 'Your Goals')

@section('styles')
<style>
    .goals-header {
        padding: 2rem 0;
        background-color: #f8f9fa;
        margin-bottom: 2rem;
    }
    
    .goal-card {
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .goal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .goal-card .card-img-top {
        height: 180px;
        object-fit: cover;
    }
    
    .goal-card .badge-corner {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .goal-card .card-body {
        padding: 1.25rem;
    }
    
    .goal-card .category-badge {
        position: absolute;
        top: 10px;
        left: 10px;
    }
    
    .goal-card .progress {
        height: 6px;
        margin-bottom: 0.5rem;
    }
    
    .priority-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 5px;
    }
    
    .priority-high {
        background-color: #dc3545;
    }
    
    .priority-medium {
        background-color: #fd7e14;
    }
    
    .priority-low {
        background-color: #0dcaf0;
    }
    
    .filter-section {
        padding: 1rem 0;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .filter-chip {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 50px;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .filter-chip:hover {
        background-color: #e9ecef;
    }
    
    .filter-chip.active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    
    .category-studies { background-color: #6f42c1; }
    .category-sports { background-color: #20c997; }
    .category-reading { background-color: #0dcaf0; }
    .category-projects { background-color: #fd7e14; }
    .category-health { background-color: #198754; }
    .category-other { background-color: #6c757d; }
    
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="goals-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="display-5 fw-bold">Your Goals</h1>
                <p class="lead text-muted">Track, manage, and achieve your goals</p>
            </div>
            <div>
                <a href="{{ route('goals.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>New Goal
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

    <div class="filter-section">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="mb-2 fw-bold">Filter by Category</label>
                    <div class="filter-group">
                        <div class="filter-chip category-filter active" data-category="all">All</div>
                        <div class="filter-chip category-filter" data-category="studies">Studies</div>
                        <div class="filter-chip category-filter" data-category="sports">Sports</div>
                        <div class="filter-chip category-filter" data-category="reading">Reading</div>
                        <div class="filter-chip category-filter" data-category="projects">Projects</div>
                        <div class="filter-chip category-filter" data-category="health">Health</div>
                        <div class="filter-chip category-filter" data-category="other">Other</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="mb-2 fw-bold">Filter by Status</label>
                    <div class="filter-group">
                        <div class="filter-chip status-filter active" data-status="all">All</div>
                        <div class="filter-chip status-filter" data-status="in-progress">In Progress</div>
                        <div class="filter-chip status-filter" data-status="completed">Completed</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="mb-2 fw-bold">Sort By</label>
                    <div class="filter-group">
                        <div class="filter-chip sort-filter active" data-sort="created-desc">Newest</div>
                        <div class="filter-chip sort-filter" data-sort="created-asc">Oldest</div>
                        <div class="filter-chip sort-filter" data-sort="deadline">Deadline</div>
                        <div class="filter-chip sort-filter" data-sort="progress-desc">Progress</div>
                        <div class="filter-chip sort-filter" data-sort="priority">Priority</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="mb-2 fw-bold">Filter by Priority</label>
                    <div class="filter-group">
                        <div class="filter-chip priority-filter active" data-priority="all">All</div>
                        <div class="filter-chip priority-filter" data-priority="high">High</div>
                        <div class="filter-chip priority-filter" data-priority="medium">Medium</div>
                        <div class="filter-chip priority-filter" data-priority="low">Low</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="goals-container">
        @forelse($goals as $goal)
            <div class="col goal-item" 
                 data-category="{{ $goal->category }}" 
                 data-status="{{ $goal->completed ? 'completed' : 'in-progress' }}" 
                 data-priority="{{ $goal->priority }}">
                <div class="card goal-card">
                    @if($goal->image)
                        <img src="{{ asset('storage/' . $goal->image) }}" class="card-img-top" alt="{{ $goal->title }}">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center">
                            <i class="bi bi-flag fs-1 text-muted"></i>
                        </div>
                    @endif
                    
                    <span class="badge rounded-pill category-{{ $goal->category }} category-badge">
                        {{ ucfirst($goal->category) }}
                    </span>
                    
                    @if($goal->completed)
                        <span class="badge bg-success badge-corner">Completed</span>
                    @elseif($goal->end_date && $goal->end_date->isPast())
                        <span class="badge bg-danger badge-corner">Overdue</span>
                    @elseif($goal->end_date && $goal->end_date->diffInDays(now()) <= 3)
                        <span class="badge bg-warning badge-corner">Due Soon</span>
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $goal->title }}</h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="priority-indicator priority-{{ $goal->priority }}"></span>
                                <small class="text-muted">{{ ucfirst($goal->priority) }} Priority</small>
                            </div>
                            <div>
                                @if($goal->end_date)
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $goal->end_date->format('M d, Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        <p class="card-text small text-muted">
                            {{ \Illuminate\Support\Str::limit($goal->description, 80) }}
                        </p>
                        
                        <div class="progress">
                            <div class="progress-bar {{ $goal->completed ? 'bg-success' : '' }}" 
                                 role="progressbar" 
                                 style="width: {{ $goal->progress }}%;" 
                                 aria-valuenow="{{ $goal->progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small">{{ $goal->progress }}% complete</span>
                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-lightbulb"></i>
                    <h3>No goals found</h3>
                    <p class="text-muted">You haven't created any goals yet. Get started by creating your first goal!</p>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Create Your First Goal
                    </a>
                </div>
            </div>
        @endforelse
    </div>
    
    <div id="no-results" class="empty-state" style="display: none;">
        <i class="bi bi-search"></i>
        <h3>No matching goals</h3>
        <p class="text-muted">No goals match your current filters. Try adjusting your filters or create a new goal.</p>
        <button class="btn btn-outline-secondary mt-3" id="reset-filters">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Filters
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter variables
        let currentCategoryFilter = 'all';
        let currentStatusFilter = 'all';
        let currentPriorityFilter = 'all';
        let currentSortFilter = 'created-desc';
        
        // Filter and sort functions
        function applyFilters() {
            const goalItems = document.querySelectorAll('.goal-item');
            let visibleCount = 0;
            
            goalItems.forEach(item => {
                const itemCategory = item.dataset.category;
                const itemStatus = item.dataset.status;
                const itemPriority = item.dataset.priority;
                
                const matchesCategory = currentCategoryFilter === 'all' || itemCategory === currentCategoryFilter;
                const matchesStatus = currentStatusFilter === 'all' || itemStatus === currentStatusFilter;
                const matchesPriority = currentPriorityFilter === 'all' || itemPriority === currentPriorityFilter;
                
                if (matchesCategory && matchesStatus && matchesPriority) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (visibleCount === 0) {
                document.getElementById('no-results').style.display = 'block';
            } else {
                document.getElementById('no-results').style.display = 'none';
            }
            
            // Apply sorting
            applySorting();
        }
        
        function applySorting() {
            const goalsContainer = document.getElementById('goals-container');
            const goalItems = Array.from(document.querySelectorAll('.goal-item'));
            
            // Filter only visible items
            const visibleItems = goalItems.filter(item => item.style.display !== 'none');
            
            switch (currentSortFilter) {
                case 'created-desc':
                    // Assuming newer items are already first, no sorting needed
                    break;
                    
                    case 'created-asc':
                    // Reverse the current order
                    visibleItems.reverse();
                    break;
                    
                case 'deadline':
                    // Sort by deadline (closest first)
                    visibleItems.sort((a, b) => {
                        const dateA = a.querySelector('.card-body small:nth-of-type(1)');
                        const dateB = b.querySelector('.card-body small:nth-of-type(1)');
                        
                        // Handle items without deadlines
                        if (!dateA) return 1;
                        if (!dateB) return -1;
                        
                        return dateA.textContent.localeCompare(dateB.textContent);
                    });
                    break;
                    
                case 'progress-desc':
                    // Sort by progress (highest first)
                    visibleItems.sort((a, b) => {
                        const progressA = parseInt(a.querySelector('.progress-bar').style.width);
                        const progressB = parseInt(b.querySelector('.progress-bar').style.width);
                        return progressB - progressA;
                    });
                    break;
                    
                case 'priority':
                    // Sort by priority (high to low)
                    visibleItems.sort((a, b) => {
                        const priorityMap = { 'high': 3, 'medium': 2, 'low': 1 };
                        const priorityA = priorityMap[a.dataset.priority] || 0;
                        const priorityB = priorityMap[b.dataset.priority] || 0;
                        return priorityB - priorityA;
                    });
                    break;
            }
            
            // Reorder DOM elements
            visibleItems.forEach(item => {
                goalsContainer.appendChild(item);
            });
        }
        
        // Event listeners for filter clicks
        document.querySelectorAll('.category-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
                this.classList.add('active');
                
                // Update filter and apply
                currentCategoryFilter = this.dataset.category;
                applyFilters();
            });
        });
        
        document.querySelectorAll('.status-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                document.querySelectorAll('.status-filter').forEach(f => f.classList.remove('active'));
                this.classList.add('active');
                
                currentStatusFilter = this.dataset.status;
                applyFilters();
            });
        });
        
        document.querySelectorAll('.priority-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                document.querySelectorAll('.priority-filter').forEach(f => f.classList.remove('active'));
                this.classList.add('active');
                
                currentPriorityFilter = this.dataset.priority;
                applyFilters();
            });
        });
        
        document.querySelectorAll('.sort-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                document.querySelectorAll('.sort-filter').forEach(f => f.classList.remove('active'));
                this.classList.add('active');
                
                currentSortFilter = this.dataset.sort;
                applyFilters();
            });
        });
        
        // Reset filters button
        document.getElementById('reset-filters').addEventListener('click', function() {
            // Reset active states
            document.querySelector('.category-filter[data-category="all"]').classList.add('active');
            document.querySelectorAll('.category-filter:not([data-category="all"])').forEach(f => f.classList.remove('active'));
            
            document.querySelector('.status-filter[data-status="all"]').classList.add('active');
            document.querySelectorAll('.status-filter:not([data-status="all"])').forEach(f => f.classList.remove('active'));
            
            document.querySelector('.priority-filter[data-priority="all"]').classList.add('active');
            document.querySelectorAll('.priority-filter:not([data-priority="all"])').forEach(f => f.classList.remove('active'));
            
            document.querySelector('.sort-filter[data-sort="created-desc"]').classList.add('active');
            document.querySelectorAll('.sort-filter:not([data-sort="created-desc"])').forEach(f => f.classList.remove('active'));
            
            // Reset filter variables
            currentCategoryFilter = 'all';
            currentStatusFilter = 'all';
            currentPriorityFilter = 'all';
            currentSortFilter = 'created-desc';
            
            // Apply filters
            applyFilters();
        });
        
        // Initial filtering
        applyFilters();
    });
</script>
@endsection