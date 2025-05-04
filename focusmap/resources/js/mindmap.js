document.addEventListener('DOMContentLoaded', function() {
    const goalForm = document.getElementById('goal-form');
    const existingGoals = document.getElementById('existing-goals');
    const loadGoalBtn = document.getElementById('load-goal-btn');
    const addStepBtn = document.getElementById('add-step-btn');
    const stepsContainer = document.getElementById('steps-container');
    const loader = document.getElementById('loader');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const saveToDb = document.getElementById('save-to-db');

    console.log('Script loaded and DOM ready');

    // Initialize GoJS Mind Map
    const $ = go.GraphObject.make;
    const myDiagram = $(go.Diagram, 'mindmap-container', {
        layout: $(go.TreeLayout, {
            angle: 90,
            nodeSpacing: 20,
            layerSpacing: 40
        })
    });

    myDiagram.nodeTemplate = $(go.Node, 'Auto',
        $(go.Shape, 'RoundedRectangle', { fill: '#4CAF50', stroke: null },
            new go.Binding('fill', 'color')
        ),
        $(go.TextBlock, { margin: 8, font: '14px sans-serif' },
            new go.Binding('text', 'key')
        )
    );

    myDiagram.linkTemplate = $(go.Link,
        { routing: go.Link.Orthogonal, corner: 5 },
        $(go.Shape, { strokeWidth: 2, stroke: '#555' })
    );

    // Initialize Leaflet Map
    const map = L.map('map-container').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Fetch existing goals
    fetch('/api/goals', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to fetch goals: ' + response.status);
        return response.json();
    })
    .then(goals => {
        console.log('Goals fetched:', goals);
        goals.forEach(goal => {
            const option = document.createElement('option');
            option.value = goal.id;
            option.textContent = goal.title;
            option.dataset.goal = JSON.stringify(goal);
            existingGoals.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error fetching goals:', error);
        errorMessage.textContent = 'Erreur lors du chargement des objectifs existants: ' + error.message;
        errorMessage.style.display = 'block';
    });

    // Add new step input
    addStepBtn.addEventListener('click', () => {
        const stepDiv = document.createElement('div');
        stepDiv.className = 'step-input';
        stepDiv.innerHTML = `
            <input type="text" class="form-control step-title" placeholder="Étape" required>
            <input type="text" class="form-control step-category" placeholder="Catégorie" required>
            <button type="button" class="btn btn-danger remove-step-btn">Supprimer</button>
        `;
        stepsContainer.appendChild(stepDiv);

        stepDiv.querySelector('.remove-step-btn').addEventListener('click', () => {
            stepsContainer.removeChild(stepDiv);
        });
    });

    // Remove step input
    stepsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-step-btn')) {
            e.target.parentElement.remove();
        }
    });

    // Load existing goal
    loadGoalBtn.addEventListener('click', () => {
        const selectedOption = existingGoals.options[existingGoals.selectedIndex];
        if (!selectedOption.value) {
            errorMessage.textContent = 'Veuillez sélectionner un objectif existant';
            errorMessage.style.display = 'block';
            return;
        }

        const goalData = JSON.parse(selectedOption.dataset.goal);
        console.log('Loading goal:', goalData);

        const formattedData = {
            title: goalData.title,
            steps: goalData.steps.map(step => ({
                title: step.title,
                category: step.category,
                tasks: [step.title]
            })),
            location: goalData.location_name ? {
                name: goalData.location_name,
                lat: goalData.latitude,
                lng: goalData.longitude
            } : null
        };

        renderMindMap(formattedData.title, formattedData.steps);
        renderMap(formattedData);
    });

    // Form Submission
    goalForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        console.log('Form submitted');
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
        loader.style.display = 'block';

        const title = document.getElementById('goal-title').value.trim();
        const description = document.getElementById('goal-description').value.trim();
        const locationName = document.getElementById('location-name').value.trim();

        if (!title) {
            errorMessage.textContent = 'Veuillez entrer un titre pour l\'objectif';
            errorMessage.style.display = 'block';
            loader.style.display = 'none';
            return;
        }

        // Collect steps
        const steps = [];
        const stepInputs = stepsContainer.querySelectorAll('.step-input');
        stepInputs.forEach(stepInput => {
            const stepTitle = stepInput.querySelector('.step-title').value.trim();
            const stepCategory = stepInput.querySelector('.step-category').value.trim();
            if (stepTitle && stepCategory) {
                steps.push({
                    title: stepTitle,
                    category: stepCategory,
                    tasks: [stepTitle]
                });
            }
        });

        if (steps.length === 0) {
            errorMessage.textContent = 'Veuillez ajouter au moins une étape';
            errorMessage.style.display = 'block';
            loader.style.display = 'none';
            return;
        }

        try {
            // Send data to backend for geocoding
            const response = await fetch('/api/generate-mindmap', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title,
                    description,
                    steps,
                    location_name: locationName || null
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error('Generate mindmap failed: ' + (errorData.error || response.statusText));
            }

            const data = await response.json();
            console.log('Generate mindmap response:', data);

            // Render the mind map and map
            renderMindMap(data.title, data.steps);
            renderMap(data);

            // Save to Database if Checked
            if (saveToDb.checked) {
                const saveResponse = await fetch('/api/save-goal-with-steps', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        title: data.title,
                        description: data.description,
                        steps: data.steps.map(step => ({
                            title: step.title,
                            category: step.category
                        })),
                        location: data.location
                    })
                });

                if (!saveResponse.ok) {
                    const errorData = await saveResponse.json();
                    throw new Error('Save failed: ' + (errorData.error || saveResponse.statusText));
                }

                successMessage.textContent = 'Mindmap généré et sauvegardé avec succès !';
                // Refresh the dropdown
                existingGoals.innerHTML = '<option value="">Sélectionner un objectif existant</option>';
                fetch('/api/goals', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(goals => {
                    goals.forEach(goal => {
                        const option = document.createElement('option');
                        option.value = goal.id;
                        option.textContent = goal.title;
                        option.dataset.goal = JSON.stringify(goal);
                        existingGoals.appendChild(option);
                    });
                });
            } else {
                successMessage.textContent = 'Mindmap généré avec succès !';
            }

            successMessage.style.display = 'block';
            loader.style.display = 'none';
        } catch (error) {
            console.error('Error:', error);
            errorMessage.textContent = error.message || 'Une erreur est survenue';
            errorMessage.style.display = 'block';
            loader.style.display = 'none';
        }
    });

    function renderMindMap(goalTitle, steps) {
        const nodes = [{ key: goalTitle, color: '#4CAF50' }];
        steps.forEach(step => {
            nodes.push({ key: step.category, color: '#2196F3', parent: goalTitle });
            if (Array.isArray(step.tasks)) {
                step.tasks.forEach(task => {
                    nodes.push({ key: task, color: '#FFC107', parent: step.category });
                });
            }
        });
        myDiagram.model = new go.TreeModel(nodes);
    }

    function renderMap(goalData) {
        if (goalData.location && goalData.location.lat && goalData.location.lng) {
            map.setView([goalData.location.lat, goalData.location.lng], 6);
            L.marker([goalData.location.lat, goalData.location.lng])
                .addTo(map)
                .bindPopup(`<b>${goalData.title}</b><br>${goalData.location.name}`)
                .openPopup();
        } else {
            map.setView([20, 0], 2); // Default view if no location
        }
    }
});