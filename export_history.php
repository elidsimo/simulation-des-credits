<?php
// Vérifier la session utilisateur
include 'check_session.php';
include 'connexion.php';

// Récupérer le format d'export (par défaut: excel)
$format = isset($_GET['format']) ? $_GET['format'] : 'excel';

// Récupérer toutes les simulations de l'utilisateur
try {
    $stmt = $conn->prepare("SELECT * FROM simulation WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $simulations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des simulations : " . $e->getMessage());
}

// Fonction pour formater les montants
function formatMontantExport($montant)
{
    return number_format($montant, 2, '.', '');
}

// Fonction pour formater les taux
function formatTauxExport($taux)
{
    return number_format($taux, 2, '.', '');
}

if ($format === 'excel') {
    // Export Excel (CSV)
    $filename = "historique_simulations_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

    // Créer le fichier CSV
    $output = fopen('php://output', 'w');

    // BOM pour UTF-8 (pour Excel)
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // En-têtes CSV
    $headers = [
        'ID',
        'Type de Crédit',
        'Montant (Dh)',
        'Durée (mois)',
        'Taux (%)',
        'Mensualité (Dh)',
        'Total à rembourser (Dh)',
        'Coût du crédit (Dh)',
        'Date de création'
    ];
    fputcsv($output, $headers, ';');

    // Données
    foreach ($simulations as $simulation) {
        $row = [
            $simulation['id'],
            $simulation['type_credit'],
            formatMontantExport($simulation['montant']),
            $simulation['duree'],
            formatTauxExport($simulation['taux']),
            formatMontantExport($simulation['mensualite']),
            formatMontantExport($simulation['total_rembourse']),
            formatMontantExport($simulation['total_rembourse'] - $simulation['montant']),
            date('d/m/Y H:i:s', strtotime($simulation['created_at']))
        ];
        fputcsv($output, $row, ';');
    }

    fclose($output);
    exit;

} elseif ($format === 'pdf') {
    // Export PDF (HTML vers PDF simple)
    $filename = "historique_simulations_" . date('Y-m-d_H-i-s') . ".pdf";

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>Historique des Simulations</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }

            .info {
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f5f5f5;
                font-weight: bold;
            }

            .amount {
                text-align: right;
            }

            .footer {
                margin-top: 30px;
                border-top: 1px solid #ccc;
                padding-top: 10px;
                font-size: 10px;
                color: #666;
            }

            @media print {
                body {
                    margin: 0;
                }
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h1>Historique des Simulations de Crédit</h1>
            <p>Utilisateur: <?php echo htmlspecialchars($user_name); ?></p>
            <p>Généré le: <?php echo date('d/m/Y à H:i:s'); ?></p>
        </div>

        <div class="info">
            <strong>Résumé:</strong><br>
            Nombre total de simulations: <?php echo count($simulations); ?><br>
            Montant total simulé:
            <?php echo number_format(array_sum(array_column($simulations, 'montant')), 2, ',', ' '); ?> Dh<br>
            <?php if (count($simulations) > 0): ?>
                Taux moyen:
                <?php echo number_format(array_sum(array_column($simulations, 'taux')) / count($simulations), 2, ',', '.'); ?> %
            <?php endif; ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type de Crédit</th>
                    <th>Montant</th>
                    <th>Durée</th>
                    <th>Taux</th>
                    <th>Mensualité</th>
                    <th>Total à rembourser</th>
                    <th>Coût du crédit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($simulations as $simulation): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($simulation['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($simulation['type_credit']); ?></td>
                        <td class="amount"><?php echo number_format($simulation['montant'], 2, ',', ' '); ?> Dh</td>
                        <td><?php echo $simulation['duree']; ?> mois</td>
                        <td class="amount"><?php echo number_format($simulation['taux'], 2, ',', '.'); ?> %</td>
                        <td class="amount"><?php echo number_format($simulation['mensualite'], 2, ',', ' '); ?> Dh</td>
                        <td class="amount"><?php echo number_format($simulation['total_rembourse'], 2, ',', ' '); ?> Dh</td>
                        <td class="amount">
                            <?php echo number_format($simulation['total_rembourse'] - $simulation['montant'], 2, ',', ' '); ?>
                            Dh
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer">
            <p>Document généré automatiquement par la Plateforme de Simulation de Crédit</p>
        </div>
    </body>

    </html>
    <?php
    exit;

} else {
    // Format non supporté
    header("Location: historique.php?error=format_non_supporté");
    exit;
}
?>