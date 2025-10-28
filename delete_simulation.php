<?php
// Démarrer la session et vérifier l'authentification
session_start();
header('Content-Type: application/json');

// DÉBOGAGE : Log des données reçues
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    error_log("Erreur: user_id non défini dans la session");
    echo json_encode(['success' => false, 'message' => 'Non autorisé - user_id manquant']);
    exit();
}

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Erreur: Méthode non POST - " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Vérifier que l'ID est fourni
if (!isset($_POST['id']) || empty($_POST['id'])) {
    error_log("Erreur: ID manquant - " . print_r($_POST, true));
    echo json_encode(['success' => false, 'message' => 'ID de simulation manquant']);
    exit();
}

// Inclure la connexion à la base de données
try {
    include 'connexion.php';
    error_log("Connexion DB réussie");
} catch (Exception $e) {
    error_log("Erreur connexion DB: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit();
}

try {
    $simulation_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];

    error_log("Tentative de suppression - ID: $simulation_id, User: $user_id");

    // Vérifier que la simulation appartient bien à l'utilisateur connecté
    $check_stmt = $conn->prepare("SELECT id FROM simulation WHERE id = :id AND user_id = :user_id");
    $check_stmt->bindParam(':id', $simulation_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->execute();

    $found_rows = $check_stmt->rowCount();
    error_log("Simulations trouvées: $found_rows");

    if ($found_rows === 0) {
        error_log("Simulation non trouvée ou non autorisée");
        echo json_encode(['success' => false, 'message' => 'Simulation non trouvée ou non autorisée']);
        exit();
    }

    // Supprimer la simulation
    $delete_stmt = $conn->prepare("DELETE FROM simulation WHERE id = :id AND user_id = :user_id");
    $delete_stmt->bindParam(':id', $simulation_id, PDO::PARAM_INT);
    $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($delete_stmt->execute()) {
        $deleted_rows = $delete_stmt->rowCount();
        error_log("Lignes supprimées: $deleted_rows");

        if ($deleted_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Simulation supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucune simulation supprimée']);
        }
    } else {
        error_log("Erreur lors de l'exécution de DELETE");
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la suppression']);
    }

} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Erreur générale: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>