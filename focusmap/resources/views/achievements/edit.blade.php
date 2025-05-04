@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit me-2"></i>
                        <h4 class="mb-0">Edit Achievement</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('achievements.update', $achievement->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title Field -->
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="fas fa-signature me-1 text-info"></i> Achievement Title
                            </label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $achievement->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description Field -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1 text-info"></i> Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" required>{{ old('description', $achievement->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Goal Selection Field -->
                        <div class="mb-3">
                            <label for="goal_id" class="form-label">
                                <i class="fas fa-bullseye me-1 text-info"></i> Associated Goal
                            </label>
                            <select class="form-select @error('goal_id') is-invalid @enderror" 
                                    id="goal_id" name="goal_id" required>
                                <option value="">-- Select Goal --</option>
                                @foreach($goals as $goal)
                                    <option value="{{ $goal->id }}" 
                                        {{ old('goal_id', $achievement->goal_id) == $goal->id ? 'selected' : '' }}>
                                        {{ $goal->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('goal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Other fields (badge_icon, achievement_type, threshold) would go here -->

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('achievements.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning text-white">
                                <i class="fas fa-save me-1"></i> Update Achievement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection