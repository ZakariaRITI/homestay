<?php
session_start();
require 'db.php'; // connexion à la BDD

// Variable pour le message d'erreur
$errorMsg = "";

// Traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $roleForm = $_POST['role'];

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            if ($roleForm === $user['role']) {
                // Connexion OK
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nom'] = $user['nom'];

                if ($user['role'] === 'ADMIN') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: logements.php");
                }
                exit();
            } else {
                $errorMsg = "Le rôle choisi ne correspond pas à votre compte.";
            }
        } else {
            $errorMsg = "Mot de passe incorrect.";
        }
    } else {
        $errorMsg = "Email non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Homestay 2030</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6f42c1, #d63384);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 20px;
            padding: 30px;
            background-color: rgba(0,0,0,0.65);
            color: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            width: 400px;
        }
        h2 { color: #fff; font-weight: 700; }
        .form-label { color: #f0f0f0; }
        .form-control { border-radius: 10px; }
        .form-select { border-radius: 10px; }
        .btn-custom {
            background-color: #d63384; border: none; font-weight: 600;
        }
        .btn-custom:hover { background-color: #b52a6e; }
        .nav-tabs .nav-link.active {
            background-color: #6f42c1; color: #fff; border-radius: 15px;
        }
        .nav-tabs .nav-link { color: #f0f0f0; border-radius: 15px; }
        .nav-tabs { border-bottom: none; justify-content: center; }
        .error-msg { color: #ff4c4c; font-weight: 600; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>

<div class="card shadow-lg">
    <h2 class="text-center mb-4">Bienvenue Homestay 2030</h2>

    <!-- Message d'erreur -->
    <?php if(!empty($errorMsg)): ?>
        <div class="error-msg"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-4" id="authTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Se connecter</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Créer un compte</button>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content">
        <!-- Login Form -->
        <div class="tab-pane fade show active" id="login" role="tabpanel">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="emailLogin" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailLogin" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label for="passwordLogin" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="passwordLogin" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vous êtes :</label>
                    <select class="form-select" name="role" required>
                        <option value="USER" <?= (isset($roleForm) && $roleForm === "USER") ? "selected" : "" ?>>Utilisateur</option>
                        <option value="ADMIN" <?= (isset($roleForm) && $roleForm === "ADMIN") ? "selected" : "" ?>>Administrateur</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-custom w-100">Se connecter</button>
            </form>
        </div>

        <!-- Register Form -->
        <div class="tab-pane fade" id="register" role="tabpanel">
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom complet</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="mb-3">
                    <label for="emailRegister" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailRegister" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="passwordRegister" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="passwordRegister" name="password" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Créer un compte</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
