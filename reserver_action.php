<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: logements.php");
    exit();
}

$logement_id = $_POST['logement_id'];
$user_id = $_SESSION['user_id'];
$date_debut = $_POST['date_debut'];
$date_fin = $_POST['date_fin'];

// Vérifier les dates
if ($date_fin <= $date_debut) {
    die("La date de fin doit être après la date de début");
}

// Récupérer le prix du logement
$stmt = $pdo->prepare("SELECT prix FROM logements WHERE id = ?");
$stmt->execute([$logement_id]);
$logement = $stmt->fetch();

if (!$logement) {
    die("Logement introuvable");
}

// Calcul nombre de jours
$debut = new DateTime($date_debut);
$fin = new DateTime($date_fin);
$nb_jours = $debut->diff($fin)->days;

// Calcul prix total
$prix_total = $logement['prix'] * $nb_jours;

// Insertion réservation
$stmt = $pdo->prepare("
    INSERT INTO reservations 
    (logement_id, user_id, date_debut, date_fin, prix_total)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $logement_id,
    $user_id,
    $date_debut,
    $date_fin,
    $prix_total
]);

header("Location: logements.php");
exit();
