@extends('layouts.app')

@section('title', 'Create New Goal')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    #locationMap {
        height: 300px;
        margin-top: 10px;
        border-radius: 5px;
    }
    
    .location-search-box {
        margin-bottom: 10px;
    }
    
    .goal-form .form-label {
        font-weight: 500;
    }
    
    .priority-selector {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    
    .priority-option {
        flex: 1;
        text-align: center;
        padding: 15px 0;
        border: 2px solid #dee2e6;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .priority-option:hover {
        background-color: #f8f9fa;
    }
    
    .priority-option.selected {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .priority-option i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 5px;
    }
    
    .priority-high i {
        color: #dc3545;
    }
    
    .priority-medium i {
        color: #fd7e14;
    }
    
    .priority-low i {
        color: #0dcaf0;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Create New Goal</h1>
                    <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Goals
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('goals.store') }}" method="POST" class="goal-form" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Goal Title</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                        id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                        id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="studies" {{ old('category') == 'studies' ? 'selected' : '' }}>Studies</option>
                                        <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>Sports</option>
                                        <option value="reading" {{ old('category') == 'reading' ? 'selected' : '' }}>Reading</option>
                                        <option value="projects" {{ old('category') == 'projects' ? 'selected' : '' }}>Projects</option>
                                        <option value="health" {{ old('category') == 'health' ? 'selected' : '' }}>Health</option>
                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label d-block">Priority</label>
                                    <div class="priority-selector">
                                        <div class="priority-option priority-high {{ old('priority') == 'high' ? 'selected' : '' }}" data-value="high">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <span>High</span>
                                        </div>
                                        <div class="priority-option priority-medium {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}" data-value="medium">
                                            <i class="bi bi-dash-circle"></i>
                                            <span>Medium</span>
                                        </div>
                                        <div class="priority-option priority-low {{ old('priority') == 'low' ? 'selected' : '' }}" data-value="low">
                                            <i class="bi bi-arrow-down-circle"></i>
                                            <span>Low</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="priority" id="priority" value="{{ old('priority', 'medium') }}">
                                    @error('priority')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="text" class="form-control date-picker @error('start_date') is-invalid @enderror" 
                                        id="start_date" name="start_date" value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Deadline</label>
                                    <input type="text" class="form-control date-picker @error('end_date') is-invalid @enderror" 
                                        id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Progress</label>
                                    <input type="range" class="form-range" id="progress" name="progress" min="0" max="100" step="5" value="{{ old('progress', 0) }}">
                                    <div class="d-flex justify-content-between">
                                        <small>0%</small>
                                        <small>50%</small>
                                        <small>100%</small>
                                    </div>
                                    <div class="text-center mt-2">
                                        <span class="badge bg-primary" id="progressValue">{{ old('progress', 0) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <div class="input-group location-search-box">
                                        <input type="text" class="form-control" id="locationSearch" placeholder="Search for a location">
                                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                    <div id="locationMap"></div>
                                    <div class="mt-2">
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="location_name" name="location_name" 
                                                placeholder="Location Name (e.g. Central Park, Home Office)" value="{{ old('location_name') }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="latitude" name="latitude" 
                                                    placeholder="Latitude" value="{{ old('latitude') }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="longitude" name="longitude" 
                                                    placeholder="Longitude" value="{{ old('longitude') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Goal Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                        id="image" name="image">
                                    <div class="form-text">Upload an image to represent your goal. Maximum file size: 2MB</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input @error('public') is-invalid @enderror" type="checkbox" 
                                        id="public" name="public" value="1" {{ old('public') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="public">
                                        Make this goal public
                                    </label>
                                    <div class="form-text">Public goals are visible to other users of FocusMap</div>
                                    @error('public')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Create Goal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date pickers
        flatpickr(".date-picker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        
        // Progress slider
        const progressInput = document.getElementById('progress');
        const progressValue = document.getElementById('progressValue');
        
        progressInput.addEventListener('input', function() {
            progressValue.textContent = this.value + '%';
        });
        
        // Priority selector
        const priorityOptions = document.querySelectorAll('.priority-option');
        const priorityInput = document.getElementById('priority');
        
        priorityOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove selection from all options
                priorityOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Select current option
                this.classList.add('selected');
                
                // Update hidden input
                priorityInput.value = this.dataset.value;
            });
        });
        
        // Map functionality
        let map, marker;
        
        function initializeMap() {
            // Set default view
            const lat = 40.7128; // New York as default
            const lng = -74.0060;
            
            map = L.map('locationMap').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add marker if coordinates exist
            if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
                const savedLat = parseFloat(document.getElementById('latitude').value);
                const savedLng = parseFloat(document.getElementById('longitude').value);
                
                marker = L.marker([savedLat, savedLng], {
                    draggable: true
                }).addTo(map);
                
                map.setView([savedLat, savedLng], 13);
                marker.on('dragend', updateMarkerPosition);
            }
            
            // Click on map to set marker
            map.on('click', function(e) {
                setMarkerPosition(e.latlng.lat, e.latlng.lng);
            });
        }
        
        function setMarkerPosition(lat, lng) {
            // Update form fields
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            
            // Update or create marker
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);
                marker.on('dragend', updateMarkerPosition);
            }
        }
        
        function updateMarkerPosition() {
            const position = marker.getLatLng();
            setMarkerPosition(position.lat, position.lng);
        }
        
        // Location search functionality
        document.getElementById('searchButton').addEventListener('click', function() {
            const searchQuery = document.getElementById('locationSearch').value;
            
            if (searchQuery.trim() !== '') {
                // Use OpenStreetMap Nominatim API for geocoding
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            // Get first result
                            const result = data[0];
                            const lat = parseFloat(result.lat);
                            const lng = parseFloat(result.lon);
                            
                            // Set marker and center map
                            map.setView([lat, lng], 15);
                            setMarkerPosition(lat, lng);
                            
                            // Set location name if empty
                            if (!document.getElementById('location_name').value) {
                                document.getElementById('location_name').value = result.display_name.split(',')[0];
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error searching for location:', error);
                    });
            }
        });
        
        // Initialize map on page load
        initializeMap();
    });
</script>
@endsection