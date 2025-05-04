@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .container-fluid { padding: 20px; }
    .card { margin-bottom: 20px; }
    #mindmap-container { height: 500px; border: 1px solid #ddd; background-color: #f8f9fa; }
    #map-container { height: 400px; border: 1px solid #ddd; }
    .form-goal { margin-bottom: 20px; }
    .loader { display: none; text-align: center; padding: 20px; }
    .error-message { display: none; color: #dc3545; margin-top: 10px; }
    .success-message { display: none; color: #198754; margin-top: 10px; }
    .input-group { display: flex; gap: 10px; align-items: center; }
    #existing-goals, #goal-title { flex: 1; }
    #generate-btn { white-space: nowrap; }
    .step-input { margin-top: 10px; display: flex; gap: 10px; }
    .step-input input { flex: 1; }
    #add-step-btn { margin-top: 10px; }
    .location-input { margin-top: 10px; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>Mindmap des Objectifs</h2>
                </div>
                <div class="card-body">
                    <div class="form-goal">
                        <div class="input-group">
                            <select id="existing-goals" class="form-control">
                                <option value="">Sélectionner un objectif existant</option>
                            </select>
                            <button id="load-goal-btn" class="btn btn-secondary">Charger</button>
                        </div>
                    </div>

                    <form id="goal-form" class="form-goal">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="goal-title" name="title" class="form-control" placeholder="Entrez un nouvel objectif" required>
                        </div>
                        <div class="form-group mt-2">
                            <textarea id="goal-description" name="description" class="form-control" placeholder="Description (optionnel)"></textarea>
                        </div>

                        <div id="steps-container">
                            <div class="step-input">
                                <input type="text" class="form-control step-title" placeholder="Étape" required>
                                <input type="text" class="form-control step-category" placeholder="Catégorie" required>
                                <button type="button" class="btn btn-danger remove-step-btn">Supprimer</button>
                            </div>
                        </div>
                        <button type="button" id="add-step-btn" class="btn btn-secondary">Ajouter une étape</button>

                        <div class="location-input">
                            <input type="text" id="location-name" name="location_name" class="form-control" placeholder="Lieu (ex. Tokyo)">
                        </div>

                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="save-to-db" checked>
                            <label class="form-check-label" for="save-to-db">Sauvegarder en base de données</label>
                        </div>

                        <button type="submit" id="generate-btn" class="btn btn-primary mt-2">Générer</button>
                    </form>

                    <div id="loader" class="loader">
                        <p>Génération en cours...</p>
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>

                    <div id="error-message" class="error-message"></div>
                    <div id="success-message" class="success-message"></div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">Mind Map</div>
                                <div class="card-body">
                                    <div id="mindmap-container"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">Carte</div>
                                <div class="card-body">
                                    <div id="map-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/gojs/release/go.js"></script>
<script src="{{ asset('js/mindmap.js') }}"></script>
@endsection