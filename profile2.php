<?php
require 'db.php';
require 'auth.php';
require_login();

$stmt = $pdo->prepare('SELECT id, nom, email, role, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (current_role() === 'ADMIN') {
    include 'admin_menu.php';
} else {
    include 'menu.php';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #7a2fc8, #e155a1);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 20px;
            background-color: rgba(0,0,0,0.65);
            color: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }
        .label {
            color: rgba(255,255,255,0.8);
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3 class="mb-4">Profil</h3>
                <div class="mb-2"><span class="label">Nom :</span> <?= htmlspecialchars($user['nom'] ?? '') ?></div>
                <div class="mb-2"><span class="label">Email :</span> <?= htmlspecialchars($user['email'] ?? '') ?></div>
                <div class="mb-2"><span class="label">Rôle :</span> <?= htmlspecialchars($user['role'] ?? '') ?></div>
                <div class="mb-4"><span class="label">Créé le :</span> <?= htmlspecialchars($user['created_at'] ?? '') ?></div>

                <a class="btn btn-warning" href="deconnexion.php">Déconnexion</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
