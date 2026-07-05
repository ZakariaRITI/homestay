<?php
session_start();
require 'db.php';
include 'menu.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logement_id = $_GET['logement_id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM logements WHERE id = ?");
$stmt->execute([$logement_id]);
$logement = $stmt->fetch();

if (!$logement) {
    die("Logement introuvable");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Réserver</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card p-4 mx-auto" style="max-width:500px;">
        <h4 class="mb-3"><?= htmlspecialchars($logement['titre']) ?></h4>
        <p>Prix / nuit : <b><?= $logement['prix'] ?> MAD</b></p>

        <form method="POST" action="reserver_action.php">
            <input type="hidden" name="logement_id" value="<?= $logement['id'] ?>">

            <label>Date début</label>
            <input type="date" name="date_debut" class="form-control mb-3" required>

            <label>Date fin</label>
            <input type="date" name="date_fin" class="form-control mb-3" required>

            <button class="btn btn-primary w-100">Confirmer la réservation</button>
        </form>
    </div>
</div>

</body>
</html>
