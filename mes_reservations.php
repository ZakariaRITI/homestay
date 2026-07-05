<?php
session_start();
require 'db.php';
include 'menu.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les réservations de l'utilisateur avec infos logement
$stmt = $pdo->prepare("
    SELECT r.*, l.titre, l.ville, l.type_logement, l.image 
    FROM reservations r 
    JOIN logements l ON r.logement_id = l.id 
    WHERE r.user_id = ? 
    ORDER BY r.date_debut DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réservations - Homestay 2030</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        h2 {
            color: #2c2c2c;
            font-weight: 600;
        }
        .card-reservation {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-reservation:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .card-reservation img {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }
        .card-reservation .card-body {
            padding: 20px;
        }
        .card-reservation h5 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #222;
        }
        .card-reservation p {
            margin-bottom: 6px;
            color: #555;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: #fff;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-cancel:hover {
            background-color: #b02a37;
        }
        @media (max-width:768px){
            .card-reservation img { height: 150px; }
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-5">Mes Réservations</h2>
    <div class="row">

        <?php if(empty($reservations)): ?>
            <p class="text-center">Vous n'avez encore aucune réservation.</p>
        <?php else: ?>
            <?php foreach($reservations as $res): ?>
            <div class="col-md-4">
                <div class="card card-reservation">
                    <img src="image/<?= htmlspecialchars($res['image']) ?>" alt="<?= htmlspecialchars($res['titre']) ?>">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($res['titre']) ?></h5>
                        <p>Ville : <?= htmlspecialchars($res['ville']) ?></p>
                        <p>Type : <?= htmlspecialchars($res['type_logement']) ?></p>
                        <p>Du : <?= htmlspecialchars($res['date_debut']) ?> au <?= htmlspecialchars($res['date_fin']) ?></p>
                        <p>Prix total : <?= number_format($res['prix_total'],2) ?> MAD</p>
                        <!-- bouton annuler (optionnel) -->
                        <form method="POST" action="annuler_reservation.php" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
                            <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="btn btn-cancel w-100">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
