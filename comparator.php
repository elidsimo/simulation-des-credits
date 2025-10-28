<?php include 'check_session.php'; ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparateur de Simulations - CreditSim</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            color: white;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #00d4ff;
            margin-bottom: 1rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.08);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-top: 0.5rem;
        }

        /* Section de sélection */
        .selection-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .selection-title {
            color: #00d4ff;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .simulations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .simulation-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.5rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .simulation-card.selected {
            border-color: #00d4ff;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
            transform: translateY(-5px);
        }

        .simulation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.2);
        }

        .simulation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .simulation-type {
            font-weight: 600;
            color: #00d4ff;
        }

        .simulation-date {
            font-size: 0.85rem;
            opacity: 0.7;
        }

        .simulation-details {
            display: grid;
            gap: 0.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: 600;
            color: #00d4ff;
        }

        .selection-checkbox {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: transparent;
            transition: all 0.3s ease;
        }

        .simulation-card.selected .selection-checkbox {
            background: #00d4ff;
            border-color: #00d4ff;
        }

        .selection-checkbox::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .simulation-card.selected .selection-checkbox::after {
            opacity: 1;
        }

        /* Boutons d'action */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00d4ff, #3a7bd5);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(0, 212, 255, 0.5);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Section de comparaison */
        .comparison-section {
            display: none;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2rem;
            overflow-x: auto;
        }

        .comparison-title {
            color: #00d4ff;
            font-size: 1.3rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .comparison-table {
            min-width: 700px;
            border-collapse: collapse;
            width: 100%;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .comparison-table th {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        .comparison-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .best-value {
            color: #00ff88;
            font-weight: 600;
            position: relative;
        }

        .best-value::after {
            content: '🏆';
            margin-left: 0.5rem;
        }

        /* Messages d'état */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            opacity: 0.7;
        }

        .empty-state i {
            font-size: 4rem;
            color: #00d4ff;
            margin-bottom: 1rem;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .comparison-section {
                padding: 1rem;
            }

            .simulations-grid {
                grid-template-columns: 1fr;
            }
        }



        .back-link {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(-5px);
        }
    </style>
</head>

<body>
    <div class="container">


        <div class="header">
            <h1><i class="fas fa-balance-scale"></i> Comparateur de Simulations</h1>
            <p>Comparez vos simulations côte à côte pour choisir l'offre la plus avantageuse</p>
            <div class="welcome-badge">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </div>

        <div class="selection-section">
            <h3 class="selection-title">
                <i class="fas fa-check-square"></i>
                Sélectionnez vos simulations à comparer (2 à 4 simulations)
            </h3>

            <div id="simulationsGrid" class="simulations-grid">
                <!-- Les simulations seront chargées ici via JavaScript -->
            </div>

            <div class="action-buttons">
                <button id="compareBtn" class="btn btn-primary" disabled>
                    <i class="fas fa-chart-bar"></i>
                    Comparer les simulations
                </button>
                <button id="clearBtn" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Effacer la sélection
                </button>
            </div>
        </div>

        <div id="comparisonSection" class="comparison-section">
            <h3 class="comparison-title">
                <i class="fas fa-analytics"></i>
                Résultats de la comparaison
            </h3>

            <div style="overflow-x: auto;">
                <table id="comparisonTable" class="comparison-table">
                    <thead>
                        <tr>
                            <th>Critères</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Type de crédit</td>
                        </tr>
                        <tr>
                            <td>Montant emprunté</td>
                        </tr>
                        <tr>
                            <td>Durée</td>
                        </tr>
                        <tr>
                            <td>Taux d'intérêt</td>
                        </tr>
                        <tr>
                            <td>Mensualité</td>
                        </tr>
                        <tr>
                            <td>Total à rembourser</td>
                        </tr>
                        <tr>
                            <td>Coût du crédit</td>
                        </tr>
                        <tr>
                            <td>Date de simulation</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="emptyState" class="empty-state">
            <i class="fas fa-calculator"></i>
            <h3>Chargement de vos simulations...</h3>
            <div class="loading"></div>
        </div>
    </div>

    <script>
        let simulations = [];
        let selectedSimulations = [];

        // Charger les simulations au chargement de la page
        document.addEventListener('DOMContentLoaded', function () {
            loadSimulations();
        });

        function loadSimulations() {
            fetch('get_simulations.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        simulations = data.simulations;
                        displaySimulations();
                    } else {
                        showEmptyState('Aucune simulation trouvée', 'Créez d\'abord des simulations pour pouvoir les comparer');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showEmptyState('Erreur de chargement', 'Impossible de charger vos simulations');
                });
        }

        function displaySimulations() {
            const grid = document.getElementById('simulationsGrid');
            const emptyState = document.getElementById('emptyState');

            if (simulations.length === 0) {
                showEmptyState('Aucune simulation disponible', 'Créez des simulations pour pouvoir les comparer');
                return;
            }

            emptyState.style.display = 'none';

            grid.innerHTML = simulations.map(sim => `
                <div class="simulation-card" data-id="${sim.id}" onclick="toggleSelection(${sim.id})">
                    <div class="selection-checkbox"></div>
                    <div class="simulation-header">
                        <span class="simulation-type">${sim.type_credit}</span>
                        <span class="simulation-date">${formatDate(sim.created_at)}</span>
                    </div>
                    <div class="simulation-details">
                        <div class="detail-row">
                            <span>Montant:</span>
                            <span>${formatMoney(sim.montant)} MAD</span>
                        </div>
                        <div class="detail-row">
                            <span>Durée:</span>
                            <span>${sim.duree} ans</span>
                        </div>
                        <div class="detail-row">
                            <span>Taux:</span>
                            <span>${sim.taux}%</span>
                        </div>
                        <div class="detail-row">
                            <span>Mensualité:</span>
                            <span>${formatMoney(sim.mensualite)} MAD</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function toggleSelection(simId) {
            const card = document.querySelector(`[data-id="${simId}"]`);
            const index = selectedSimulations.indexOf(simId);

            if (index > -1) {
                // Désélectionner
                selectedSimulations.splice(index, 1);
                card.classList.remove('selected');
            } else {
                // Sélectionner (max 4)
                if (selectedSimulations.length < 4) {
                    selectedSimulations.push(simId);
                    card.classList.add('selected');
                } else {
                    alert('Vous ne pouvez sélectionner que 4 simulations maximum');
                    return;
                }
            }

            updateCompareButton();
        }

        function updateCompareButton() {
            const compareBtn = document.getElementById('compareBtn');
            if (selectedSimulations.length >= 2) {
                compareBtn.disabled = false;
                compareBtn.innerHTML = `<i class="fas fa-chart-bar"></i> Comparer ${selectedSimulations.length} simulations`;
            } else {
                compareBtn.disabled = true;
                compareBtn.innerHTML = '<i class="fas fa-chart-bar"></i> Sélectionnez au moins 2 simulations';
            }
        }

        function clearSelection() {
            selectedSimulations = [];
            document.querySelectorAll('.simulation-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.getElementById('comparisonSection').style.display = 'none';
            updateCompareButton();
        }

        function compareSimulations() {
            if (selectedSimulations.length < 2) return;

            const selectedSims = simulations.filter(sim => selectedSimulations.includes(parseInt(sim.id)));
            displayComparison(selectedSims);
        }

        function displayComparison(sims) {
            const section = document.getElementById('comparisonSection');
            const table = document.getElementById('comparisonTable');

            // Construire le header
            const headers = ['Critères', ...sims.map((sim, i) => `Simulation ${i + 1}`)];
            table.querySelector('thead tr').innerHTML = headers.map(h => `<th>${h}</th>`).join('');

            // Construire les données avec identification des meilleures valeurs
            const rows = [
                ['Type de crédit', ...sims.map(s => s.type_credit)],
                ['Montant emprunté', ...sims.map(s => formatMoney(s.montant) + ' MAD')],
                ['Durée', ...sims.map(s => s.duree + ' ans')],
                ['Taux d\'intérêt', ...sims.map(s => s.taux + '%')],
                ['Mensualité', ...sims.map(s => formatMoney(s.mensualite) + ' MAD')],
                ['Total à rembourser', ...sims.map(s => formatMoney(s.total_rembourse) + ' MAD')],
                ['Coût du crédit', ...sims.map(s => formatMoney(s.total_rembourse - s.montant) + ' MAD')],
                ['Date de simulation', ...sims.map(s => formatDate(s.created_at))]
            ];

            // Identifier les meilleures valeurs (plus bas pour mensualité, total et coût)
            const bestIndices = {
                mensualite: findBestIndex(sims, 'mensualite', true),
                total_rembourse: findBestIndex(sims, 'total_rembourse', true),
                cout: findBestIndex(sims, s => s.total_rembourse - s.montant, true)
            };

            table.querySelector('tbody').innerHTML = rows.map((row, rowIndex) => {
                return '<tr>' + row.map((cell, cellIndex) => {
                    let className = '';
                    if (cellIndex > 0) {
                        if (rowIndex === 4 && cellIndex - 1 === bestIndices.mensualite) className = 'best-value';
                        if (rowIndex === 5 && cellIndex - 1 === bestIndices.total_rembourse) className = 'best-value';
                        if (rowIndex === 6 && cellIndex - 1 === bestIndices.cout) className = 'best-value';
                    }
                    return `<td class="${className}">${cell}</td>`;
                }).join('') + '</tr>';
            }).join('');

            section.style.display = 'block';
            section.scrollIntoView({ behavior: 'smooth' });
        }

        function findBestIndex(sims, field, lowest = true) {
            const values = sims.map(s => typeof field === 'function' ? field(s) : s[field]);
            const bestValue = lowest ? Math.min(...values) : Math.max(...values);
            return values.indexOf(bestValue);
        }

        function showEmptyState(title, description) {
            const emptyState = document.getElementById('emptyState');
            emptyState.innerHTML = `
                <i class="fas fa-calculator"></i>
                <h3>${title}</h3>
                <p>${description}</p>
            `;
            emptyState.style.display = 'block';
            document.getElementById('simulationsGrid').style.display = 'none';
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-MA').format(amount);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Event listeners
        document.getElementById('compareBtn').addEventListener('click', compareSimulations);
        document.getElementById('clearBtn').addEventListener('click', clearSelection);
    </script>
</body>

</html>