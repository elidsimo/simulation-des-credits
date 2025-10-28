<?php
require_once 'connexion.php';

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion avec un message
header('Location: login.html');
exit;
?>