<?php
session_start();
require 'db.php';

echo "<pre>";
print_r($_POST);
echo "</pre>";
?>

<?php
session_start();
require 'db.php'; // Assure-toi que db.php est bien au même niveau

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Forcer le rôle USER pour toutes les inscriptions publiques
    $role = 'USER';

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo "Cet email est déjà utilisé.";
        exit;
    }

    // Hasher le mot de passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insérer dans la base
    $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$nom, $email, $passwordHash, $role])) {
        echo "Compte créé avec succès !";
        // Optionnel : rediriger vers login
        header("Location: index.php");
        exit;
    } else {
        echo "Erreur lors de la création du compte.";
    }

} else {
    echo "Méthode non autorisée.";
}
?>
