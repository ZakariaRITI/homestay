<?php
$host = "localhost";
$dbname = "homestay2030";
$user = "root";
$pass = ""; // vide si pas de mot de passe MySQL

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    // echo "Connexion OK"; // Ne mets pas ça ici, on teste via test_db.php
} catch (PDOException $e) {
    die("Erreur connexion base de données : " . $e->getMessage());
}
