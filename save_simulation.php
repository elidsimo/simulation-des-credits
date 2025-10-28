<?php
// Démarrer la session
session_start();

// Inclusion du fichier de connexion
require_once 'connexion.php';

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupérer les données JSON envoyées
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Vérifier que les données sont valides
    if (!$data) {
        throw new Exception('Données JSON invalides');
    }

    // Valider les champs requis
    $required_fields = ['type_credit', 'montant', 'duree', 'taux', 'mensualite', 'total_rembourse'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            throw new Exception("Le champ '$field' est requis");
        }
    }

    // Validation des données
    if ($data['montant'] <= 0) {
        throw new Exception('Le montant doit être supérieur à 0');
    }

    if ($data['duree'] <= 0) {
        throw new Exception('La durée doit être supérieure à 0');
    }

    if ($data['taux'] < 0 || $data['taux'] > 100) {
        throw new Exception('Le taux doit être entre 0 et 100');
    }

    // Récupérer l'ID de l'utilisateur connecté depuis la session
    $user_id = $_SESSION['user_id'];

    // Préparer la requête SQL
    $sql = "INSERT INTO simulation (user_id, type_credit, montant, duree, taux, mensualite, total_rembourse) 
            VALUES (:user_id, :type_credit, :montant, :duree, :taux, :mensualite, :total_rembourse)";

    $stmt = $conn->prepare($sql);

    // Exécuter la requête avec les données
    $result = $stmt->execute([
        ':user_id' => $user_id,
        ':type_credit' => $data['type_credit'],
        ':montant' => $data['montant'],
        ':duree' => $data['duree'],
        ':taux' => $data['taux'],
        ':mensualite' => $data['mensualite'],
        ':total_rembourse' => $data['total_rembourse']
    ]);

    if ($result) {
        // Récupérer l'ID de la simulation créée
        $simulation_id = $conn->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Simulation enregistrée avec succès',
            'simulation_id' => $simulation_id
        ]);
    } else {
        throw new Exception('Erreur lors de l\'enregistrement');
    }

} catch (Exception $e) {
    // En cas d'erreur
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur : ' . $e->getMessage()
    ]);
} catch (PDOException $e) {
    // En cas d'erreur de base de données
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données : ' . $e->getMessage()
    ]);
}
?>