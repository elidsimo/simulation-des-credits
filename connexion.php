<?php
// Informations de connexion à la base de données
$host = "localhost";       // Serveur MySQL (XAMPP : localhost)
$dbname = "simulation_credit"; // Nom de la base de données
$username = "root";        // Utilisateur MySQL par défaut sur XAMPP
$password = "";            // Mot de passe par défaut vide sur XAMPP

try {
    // Création d'une connexion PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Définir le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connexion réussie !"; // Pour tester la connexion
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>