<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Homestay 2030</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fond dégradé mauve */
        body {
            background: linear-gradient(135deg, #6f42c1, #d63384);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Card principale */
        .card {
            border-radius: 20px;
            padding: 30px;
            background-color: rgba(0,0,0,0.65); /* semi-transparent noir pour contraste */
            color: #fff; /* texte blanc par défaut */
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }

        h2 {
            color: #fff;
            font-weight: 700;
        }

        /* Labels et inputs */
        .form-label {
            color: #f0f0f0; /* blanc clair */
        }
        .form-control {
            border-radius: 10px;
        }
        .form-select {
            border-radius: 10px;
        }

        /* Boutons */
        .btn-custom {
            background-color: #d63384;
            border: none;
            font-weight: 600;
        }
        .btn-custom:hover {
            background-color: #b52a6e;
        }

        /* Tabs */
        .nav-tabs .nav-link.active {
            background-color: #6f42c1;
            color: #fff;
            border-radius: 15px;
        }
        .nav-tabs .nav-link {
            color: #f0f0f0;
            border-radius: 15px;
        }
        .nav-tabs {
            border-bottom: none;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="card shadow-lg" style="width: 400px;">
    <h2 class="text-center mb-4">Bienvenue Homestay 2030</h2>

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
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="emailLogin" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailLogin" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="passwordLogin" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="passwordLogin" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vous êtes :</label>
                    <select class="form-select" name="role" required>
                        <option value="USER">Utilisateur</option>
                        <option value="ADMIN">Administrateur</option>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
