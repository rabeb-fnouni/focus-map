@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-trophy me-2"></i>
                        <h4 class="mb-0">Create New Achievement</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('achievements.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-signature me-1 text-info"></i> Achievement Name
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1 text-info"></i> Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="goal_id" class="form-label">
                                <i class="fas fa-bullseye me-1 text-info"></i> Associated Goal
                            </label>
                            <select class="form-select @error('goal_id') is-invalid @enderror" 
                                    id="goal_id" name="goal_id">
                                <option value="">-- Select Goal --</option>
                                @foreach($goals as $goal)
                                    <option value="{{ $goal->id }}" {{ old('goal_id') == $goal->id ? 'selected' : '' }}>
                                        {{ $goal->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('goal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="badge_icon" class="form-label">
                                <i class="fas fa-icons me-1 text-info"></i> Badge Icon
                            </label>
                            <select class="form-select @error('badge_icon') is-invalid @enderror" 
                                    id="badge_icon" name="badge_icon" required>
                                <option value="">-- Select Icon --</option>
                                <option value="fas fa-trophy" {{ old('badge_icon') == 'fas fa-trophy' ? 'selected' : '' }}>Gold Trophy</option>
                                <option value="fas fa-medal" {{ old('badge_icon') == 'fas fa-medal' ? 'selected' : '' }}>Silver Medal</option>
                                <option value="fas fa-award" {{ old('badge_icon') == 'fas fa-award' ? 'selected' : '' }}>Bronze Award</option>
                                <option value="fas fa-star" {{ old('badge_icon') == 'fas fa-star' ? 'selected' : '' }}>Star</option>
                                <option value="fas fa-certificate" {{ old('badge_icon') == 'fas fa-certificate' ? 'selected' : '' }}>Certificate</option>
                            </select>
                            @error('badge_icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="achievement_type" class="form-label">
                                <i class="fas fa-tags me-1 text-info"></i> Achievement Type
                            </label>
                            <select class="form-select @error('achievement_type') is-invalid @enderror" 
                                    id="achievement_type" name="achievement_type" required>
                                <option value="">-- Select Type --</option>
                                <option value="completion" {{ old('achievement_type') == 'completion' ? 'selected' : '' }}>Goal Completion</option>
                                <option value="streak" {{ old('achievement_type') == 'streak' ? 'selected' : '' }}>Consistency Streak</option>
                                <option value="milestone" {{ old('achievement_type') == 'milestone' ? 'selected' : '' }}>Milestone</option>
                                <option value="special" {{ old('achievement_type') == 'special' ? 'selected' : '' }}>Special Achievement</option>
                            </select>
                            @error('achievement_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="threshold" class="form-label">
                                <i class="fas fa-sliders-h me-1 text-info"></i> Threshold
                            </label>
                            <input type="number" class="form-control @error('threshold') is-invalid @enderror" 
                                   id="threshold" name="threshold" value="{{ old('threshold') }}" min="1" required>
                            <small class="text-muted">The number required to earn this achievement</small>
                            @error('threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('achievements.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Achievement
                            </button>
                        </div>
                    </form>
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
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px;
    }
</style>
@endsection