<?php
session_start();
require_once "db.php"; 
include "menu.php";

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos utilisateur
$stmt = $pdo->prepare("SELECT id, nom, email, role, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$message = "";
if($_SERVER['REQUEST_METHOD'] === "POST"){
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    if(!empty($nom) && !empty($email)){
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $user_id]);
        if($check->rowCount() > 0){
            $message = "Cet email est déjà utilisé par un autre compte !";
        } else {
            // Construire la requête UPDATE
            if(!empty($password)){
                if($password !== $password_confirm){
                    $message = "Les mots de passe ne correspondent pas !";
                } else {
                    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                    $update = $pdo->prepare("UPDATE users SET nom = ?, email = ?, password = ? WHERE id = ?");
                    $update->execute([$nom, $email, $password_hashed, $user_id]);
                    $message = "Profil et mot de passe mis à jour avec succès !";
                }
            } else {
                // Pas de changement de mot de passe
                $update = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
                $update->execute([$nom, $email, $user_id]);
                $message = "Profil mis à jour avec succès !";
            }

            // Recharger les infos
            $stmt = $pdo->prepare("SELECT id, nom, email, role, created_at FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        }
    } else {
        $message = "Veuillez remplir tous les champs !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - Homestay 2030</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f9;
            min-height: 100vh;
            padding-bottom: 50px;
        }
        .profile-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .profile-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #6a5acd;
        }
        .form-control:focus {
            border-color: #6a5acd;
            box-shadow: 0 0 8px rgba(106,90,205,0.2);
        }
        .btn-update {
            background: linear-gradient(135deg, #6a5acd, #8a2be2);
            color: #fff;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .info-static {
            background: #f1f1f1;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .message {
            text-align: center;
            font-weight: 600;
            margin-bottom: 15px;
            color: #28a745;
        }
    </style>
</head>
<body>

<div class="container profile-container">
    <h2>Mon Profil</h2>

    <?php if(!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>ID (non modifiable)</label>
        <div class="info-static"><?= htmlspecialchars($user['id']) ?></div>

        <label>Nom</label>
        <input type="text" name="nom" class="form-control mb-3" value="<?= htmlspecialchars($user['nom']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" class="form-control mb-3" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Nouveau mot de passe</label>
        <input type="password" name="password" class="form-control mb-3" placeholder="Laisser vide pour ne pas changer">

        <label>Confirmer mot de passe</label>
        <input type="password" name="password_confirm" class="form-control mb-3" placeholder="Confirmer le mot de passe">

        <label>Role (non modifiable)</label>
        <div class="info-static"><?= htmlspecialchars($user['role']) ?></div>

        <label>Date de création du compte</label>
        <div class="info-static"><?= htmlspecialchars($user['created_at']) ?></div>

        <button type="submit" class="btn btn-update w-100">Mettre à jour</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
