<?php
$host = "localhost";
$nom_base = "ivoirmed";
$utilisateur = "root";
$mot_de_passe = "";

try {
    $connexion = new PDO(
        "mysql:host=$host;dbname=$nom_base;charset=utf8mb4",
        $utilisateur,
        $mot_de_passe,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $erreur) {
    die("Impossible de se connecter à la base de données. Vérifie config/database.php et MySQL.");
}
