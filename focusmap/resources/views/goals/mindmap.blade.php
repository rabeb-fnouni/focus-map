@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Mindmap des Objectifs</h1>
                    <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour aux objectifs
                    </a>
                </div>
                
                <div class="card-body">
                    <div id="mindmap-container" style="height: 600px; border: 1px solid #eee; border-radius: 5px;">
                        <!-- Le mindmap sera affiché ici -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Inclure les bibliothèques JS pour le mindmap -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le mindmap
    const chart = echarts.init(document.getElementById('mindmap-container'));
    
    // Données exemple - à remplacer par vos données réelles
    const option = {
        tooltip: {},
        series: [{
            type: 'tree',
            data: [{
                name: 'Objectif Principal',
                children: [
                    { name: 'Sous-objectif 1' },
                    { name: 'Sous-objectif 2',
                      children: [
                          { name: 'Tâche 1' },
                          { name: 'Tâche 2' }
                      ]
                    }
                ]
            }],
            symbolSize: 10,
            orient: 'vertical',
            label: {
                position: 'top',
                rotate: -90,
                verticalAlign: 'middle',
                align: 'right',
                fontSize: 16
            },
            leaves: {
                label: {
                    position: 'right',
                    verticalAlign: 'middle',
                    align: 'left'
                }
            },
            emphasis: {
                focus: 'descendant'
            },
            expandAndCollapse: true,
            animationDuration: 550,
            animationDurationUpdate: 750
        }]
    };
    
    chart.setOption(option);
    
    // Redimensionner le graphique quand la fenêtre change de taille
    window.addEventListener('resize', function() {
        chart.resize();
    });
});
</script>
@endsection