<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['logement_temp'])){
    header("Location: ajouter_logement.php");
    exit();
}

$logement = $_SESSION['logement_temp'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $prix_final = $_POST['prix_final'];

    // Insertion dans la DB
    $stmt = $pdo->prepare("INSERT INTO logements 
        (user_id, titre, ville, type_logement, capacite, equipements, image, prix, prix_suggere_ia) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $logement['user_id'],
        $logement['titre'],
        $logement['ville'],
        $logement['type_logement'],
        $logement['capacite'],
        $logement['equipements'],
        $logement['image'],
        $prix_final,
        $logement['prix_pred']
    ]);

    unset($_SESSION['logement_temp']);

    // Redirection directe vers la page des logements
    header("Location: logements.php");
    exit();
}

// Inclure le menu
include "menu.php";
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f4f9;
    margin: 0;
    padding: 0;
}

.confirm-container {
    max-width: 500px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    text-align: center;
}

.confirm-container h2 {
    color: #4B0082;
    margin-bottom: 20px;
}

.prix-info {
    font-size: 1.1rem;
    margin-bottom: 20px;
    text-align: left;
}

.prix-info strong {
    color: #ff6b81;
}

.confirm-container input[type="number"] {
    width: 80%;
    padding: 12px 15px;
    margin: 15px 0;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

.confirm-container input:focus {
    border-color: #6a5acd;
    box-shadow: 0 0 8px rgba(106, 90, 205, 0.3);
    outline: none;
}

.confirm-container button {
    padding: 12px 20px;
    border: none;
    border-radius: 50px;
    background: linear-gradient(135deg, #6a5acd, #8a2be2);
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
}

.confirm-container button:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>

<div class="confirm-container">
    <h2>Confirmation du prix</h2>
    <div class="prix-info">
        <p>Prix que vous avez proposé: <strong><?= htmlspecialchars($logement['prix_user']) ?> MAD</strong></p>
        <p>Prix suggéré par l'IA: <strong><?= htmlspecialchars($logement['prix_pred']) ?> MAD</strong></p>
    </div>

    <form method="POST">
        <label>Acceptez-vous le prix IA ou souhaitez-vous modifier le prix final ?</label><br>
        <input type="number" step="0.01" name="prix_final" value="<?= htmlspecialchars($logement['prix_pred']) ?>" required><br>
        <button type="submit">Confirmer</button>
    </form>
</div>
