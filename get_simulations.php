<?php
// Vérifier la session utilisateur
include 'check_session.php';
include 'connexion.php';

// Headers pour JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Récupérer toutes les simulations de l'utilisateur
    $stmt = $conn->prepare("
        SELECT 
            id,
            type_credit,
            montant,
            duree,
            taux,
            mensualite,
            total_rembourse,
            created_at
        FROM simulation 
        WHERE user_id = :user_id 
        ORDER BY created_at DESC
    ");

    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $simulations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatage des données
    foreach ($simulations as &$simulation) {
        // Convertir les valeurs numériques
        $simulation['montant'] = floatval($simulation['montant']);
        $simulation['duree'] = floatval($simulation['duree']);
        $simulation['taux'] = floatval($simulation['taux']);
        $simulation['mensualite'] = floatval($simulation['mensualite']);
        $simulation['total_rembourse'] = floatval($simulation['total_rembourse']);

        // Formatage des types de crédit pour l'affichage
        switch ($simulation['type_credit']) {
            case 'immobilier':
                $simulation['type_credit'] = 'Crédit Immobilier';
                break;
            case 'auto':
                $simulation['type_credit'] = 'Crédit Auto';
                break;
            case 'personnel':
                $simulation['type_credit'] = 'Crédit Personnel';
                break;
            case 'travaux':
                $simulation['type_credit'] = 'Crédit Travaux';
                break;
        }
    }

    // Réponse de succès
    echo json_encode([
        'success' => true,
        'simulations' => $simulations,
        'count' => count($simulations),
        'message' => count($simulations) > 0
            ? count($simulations) . ' simulation(s) trouvée(s)'
            : 'Aucune simulation trouvée'
    ]);

} catch (PDOException $e) {
    // Erreur de base de données
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des simulations',
        'error' => $e->getMessage()
    ]);

} catch (Exception $e) {
    // Autres erreurs
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur',
        'error' => $e->getMessage()
    ]);
}
?>