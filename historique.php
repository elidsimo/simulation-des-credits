<?php
// Vérifier la session utilisateur
include 'check_session.php';
include 'connexion.php';

// Récupérer toutes les simulations de l'utilisateur connecté
try {
    $stmt = $conn->prepare("SELECT * FROM simulation WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $simulations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des simulations : " . $e->getMessage();
    $simulations = [];
}

// Fonction pour formater les montants
function formatMontant($montant)
{
    return number_format($montant, 2, ',', ' ') . ' MAD DH';
}

// Fonction pour formater les pourcentages
function formatTaux($taux)
{
    return number_format($taux, 2, ',', '.') . ' %';
}

// Fonction pour formater la date
function formatDate($date)
{
    return date('d/m/Y à H:i', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Simulations - Plateforme Crédit</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --dark-color: #34495e;
            --light-bg: #f8f9fa;
        }

        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            margin: 2rem 0;
        }

        .page-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .user-info {
            color: var(--dark-color);
            font-size: 1.1rem;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--secondary-color), #5dade2);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .simulation-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--secondary-color);
            transition: all 0.3s ease;
        }

        .simulation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .credit-type {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .credit-immobilier {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
        }

        .credit-auto {
            background: linear-gradient(45deg, #4834d4, #686de0);
            color: white;
        }

        .credit-consommation {
            background: linear-gradient(45deg, #00d2d3, #54a0ff);
            color: white;
        }

        .credit-autres {
            background: linear-gradient(45deg, #5f27cd, #8854d0);
            color: white;
        }

        .simulation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .detail-item {
            text-align: center;
            padding: 0.8rem;
            background: var(--light-bg);
            border-radius: 8px;
        }

        .detail-label {
            font-size: 0.85rem;
            color: var(--dark-color);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .no-simulations {
            text-align: center;
            padding: 3rem;
            color: var(--dark-color);
        }

        .no-simulations i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #bdc3c7;
        }

        .btn-custom {
            background: linear-gradient(45deg, var(--secondary-color), #5dade2);
            border: none;
            border-radius: 25px;
            padding: 0.7rem 2rem;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-sm-custom {
            padding: 0.4rem 1rem;
            border-radius: 15px;
            font-size: 0.8rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-delete {
            background: var(--danger-color);
            color: white;
        }

        .btn-sm-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .date-info {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .simulation-details {
                grid-template-columns: 1fr 1fr;
            }

            .main-container {
                margin: 1rem;
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="page-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historique des Simulations
                    </h1>
                    <p class="user-info mb-0">
                        <i class="fas fa-user me-2"></i>
                        Connecté en tant que : <?php echo htmlspecialchars($user_name); ?>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistiques -->
        <?php if (!empty($simulations)): ?>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <i class="fas fa-calculator fa-2x mb-2"></i>
                        <h3><?php echo count($simulations); ?></h3>
                        <p class="mb-0">Simulations Totales</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <i class="fas fa-coins fa-2x mb-2"></i>
                        <h3><?php
                        $total_montant = array_sum(array_column($simulations, 'montant'));
                        echo formatMontant($total_montant);
                        ?></h3>
                        <p class="mb-0">Montant Total Simulé</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h3><?php
                        $taux_moyen = count($simulations) > 0 ? array_sum(array_column($simulations, 'taux')) / count($simulations) : 0;
                        echo formatTaux($taux_moyen);
                        ?></h3>
                        <p class="mb-0">Taux Moyen</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Container principal -->
        <div class="main-container">
            <?php if (empty($simulations)): ?>
                <!-- Aucune simulation -->
                <div class="no-simulations">
                    <i class="fas fa-search"></i>
                    <h3>Aucune simulation trouvée</h3>
                    <p>Vous n'avez encore effectué aucune simulation de crédit.</p>
                    <a href="simulation.php" class="btn btn-custom mt-3">
                        <i class="fas fa-plus me-2"></i>
                        Créer une simulation
                    </a>
                </div>
            <?php else: ?>
                <!-- Liste des simulations -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <i class="fas fa-list me-2"></i>
                        Vos Simulations (<?php echo count($simulations); ?>)
                    </h3>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2" onclick="printHistory()">
                            <i class="fas fa-print me-1"></i>
                            Imprimer
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="exportHistory()">
                            <i class="fas fa-download me-1"></i>
                            Exporter
                        </button>
                    </div>
                </div>

                <?php foreach ($simulations as $simulation): ?>
                    <div class="simulation-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span
                                    class="credit-type credit-<?php echo strtolower(str_replace([' ', 'é', 'è', 'ê'], ['', 'e', 'e', 'e'], $simulation['type_credit'])); ?>">
                                    <?php echo htmlspecialchars($simulation['type_credit']); ?>
                                </span>
                                <p class="date-info mt-2 mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    Simulé le <?php echo formatDate($simulation['created_at']); ?>
                                </p>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-sm-custom btn-delete"
                                    onclick="deleteSimulation(<?php echo $simulation['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="simulation-details">
                            <div class="detail-item">
                                <div class="detail-label">Montant</div>
                                <div class="detail-value"><?php echo formatMontant($simulation['montant']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Durée</div>
                                <div class="detail-value"><?php echo $simulation['duree']; ?> mois</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Taux</div>
                                <div class="detail-value"><?php echo formatTaux($simulation['taux']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Mensualité</div>
                                <div class="detail-value"><?php echo formatMontant($simulation['mensualite']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Total à rembourser</div>
                                <div class="detail-value"><?php echo formatMontant($simulation['total_rembourse']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Coût du crédit</div>
                                <div class="detail-value" style="color: var(--danger-color);">
                                    <?php echo formatMontant($simulation['total_rembourse'] - $simulation['montant']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteSimulation(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette simulation ?')) {
                // Envoyer une requête AJAX pour supprimer la simulation
                fetch('delete_simulation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Simulation supprimée avec succès');
                            location.reload();
                        } else {
                            alert('Erreur lors de la suppression : ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la suppression');
                    });
            }
        }

        function printHistory() {
            window.print();
        }

        function exportHistory() {
            // Rediriger vers un script d'export (à créer)
            window.location.href = 'export_history.php';
        }

        // Animation d'entrée pour les cartes de simulation
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.simulation-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>

</html>