<?php
session_start();
require_once "connexion.php";

// ✅ Fonction améliorée pour rediriger avec message
function redirectWithMessage($page, $type, $message)
{
    $encodedMessage = urlencode($message);
    header("Location: {$page}?{$type}={$encodedMessage}");
    exit();
}

// ✅ Fonction pour loguer les erreurs en mode debug
function logError($message)
{
    error_log("[LOGIN DEBUG] " . $message);
}

// Vérifier que le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et nettoyer les données
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    logError("Tentative de connexion pour: " . $email);

    // Validation côté serveur
    if (empty($email) || empty($mot_de_passe)) {
        logError("Champs vides détectés");
        redirectWithMessage('login.html', 'error', 'Veuillez remplir tous les champs.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logError("Format email invalide: " . $email);
        redirectWithMessage('login.html', 'error', 'Format d\'email invalide.');
    }

    try {
        // Vérifier si l'utilisateur existe
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            logError("Utilisateur trouvé: " . $user['nom']);

            // Vérifier le mot de passe hashé
            if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                logError("Mot de passe correct");

                // Stocker les infos dans la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['logged_in'] = true;

                // ✅ Vérifier que dash.html existe, sinon utiliser une page par défaut
                if (file_exists('dash.html')) {
                    redirectWithMessage('dash.html', 'success', 'Connexion réussie ! Bienvenue ' . $user['nom']);
                } else {
                    // Si dash.html n'existe pas, créer une page de redirection temporaire
                    redirectWithMessage('dashboard.php', 'success', 'Connexion réussie ! Bienvenue ' . $user['nom']);
                }
            } else {
                logError("Mot de passe incorrect");
                redirectWithMessage('login.html', 'error', 'Mot de passe incorrect.');
            }
        } else {
            logError("Utilisateur non trouvé");
            redirectWithMessage('login.html', 'error', 'Aucun compte associé à cette adresse email.');
        }

    } catch (PDOException $e) {
        // Erreur de base de données
        logError("Erreur PDO: " . $e->getMessage());
        redirectWithMessage('login.html', 'error', 'Erreur de connexion. Veuillez réessayer.');
    } catch (Exception $e) {
        // Autres erreurs
        logError("Erreur générale: " . $e->getMessage());
        redirectWithMessage('login.html', 'error', 'Une erreur inattendue s\'est produite.');
    }

} else {
    // Accès direct au fichier sans POST
    logError("Accès direct au fichier détecté");
    redirectWithMessage('login.html', 'error', 'Accès non autorisé.');
}
?>