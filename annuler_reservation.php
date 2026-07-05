<?php
session_start();
require_once "db.php";

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier si l'ID de réservation est fourni
if(!isset($_POST['reservation_id'])){
    header("Location: mes_reservations.php");
    exit();
}

$reservation_id = intval($_POST['reservation_id']);

// Vérifier que la réservation appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $user_id]);
$reservation = $stmt->fetch();

if(!$reservation){
    // Si pas trouvée ou pas autorisé
    header("Location: mes_reservations.php");
    exit();
}

// Supprimer la réservation
$stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->execute([$reservation_id]);

// Redirection vers mes réservations
header("Location: mes_reservations.php");
exit();
?>
