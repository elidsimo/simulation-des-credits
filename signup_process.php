<?php
session_start();
header('Content-Type: application/json');

// Inclure le fichier de connexion
require_once 'connexion.php';

// Fonction pour nettoyer les données d'entrée
function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

// Fonction pour valider l'email
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fonction pour valider le mot de passe
function validatePassword($password)
{
    // Au moins 6 caractères
    if (strlen($password) < 6) {
        return false;
    }
    return true;
}

// Vérifier si la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

try {
    // Récupérer et nettoyer les données du formulaire
    $nom = sanitizeInput($_POST['nom'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des données
    $errors = [];

    // Validation du nom
    if (empty($nom)) {
        $errors[] = 'Le nom est requis';
    } elseif (strlen($nom) < 2) {
        $errors[] = 'Le nom doit contenir au moins 2 caractères';
    }

    // Validation de l'email
    if (empty($email)) {
        $errors[] = 'L\'email est requis';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Format d\'email invalide';
    }

    // Validation du mot de passe
    if (empty($mot_de_passe)) {
        $errors[] = 'Le mot de passe est requis';
    } elseif (!validatePassword($mot_de_passe)) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
    }

    // Validation de la confirmation du mot de passe
    if (empty($confirm_password)) {
        $errors[] = 'La confirmation du mot de passe est requise';
    } elseif ($mot_de_passe !== $confirm_password) {
        $errors[] = 'Les mots de passe ne correspondent pas';
    }

    // Si des erreurs existent, les retourner
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }

    // Vérifier si l'email existe déjà
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cet email est déjà utilisé'
        ]);
        exit;
    }

    // Hacher le mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insérer l'utilisateur dans la base de données
    $insertQuery = "INSERT INTO users (nom, email, mot_de_passe, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt->execute([$nom, $email, $mot_de_passe_hash])) {
        // Récupérer l'ID du nouvel utilisateur
        $userId = $conn->lastInsertId();

        // Créer une session pour l'utilisateur (optionnel)
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => 'Compte créé avec succès !',
            'redirect' => 'login.html' // ou la page vers laquelle rediriger
        ]);

    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la création du compte'
        ]);
    }

} catch (PDOException $e) {
    // Log l'erreur (en production, ne pas afficher l'erreur complète)
    error_log("Erreur base de données : " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données. Veuillez réessayer.'
    ]);

} catch (Exception $e) {
    // Log l'erreur
    error_log("Erreur générale : " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue. Veuillez réessayer.'
    ]);
}
?>