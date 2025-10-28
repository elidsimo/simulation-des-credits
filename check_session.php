<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Si non connecté, rediriger vers la page de login
    header("Location: login.html");
    exit();
}

// Optionnel : récupérer les informations de l'utilisateur pour affichage
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Utilisateur';
$user_id = $_SESSION['user_id'];
?>