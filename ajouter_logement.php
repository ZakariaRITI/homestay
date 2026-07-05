<?php
session_start();
require_once "db.php"; // connexion à la DB
include "menu.php"; // inclure le menu

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un logement - Homestay 2030</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Body général */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        /* Wrapper pour encapsuler le formulaire et éviter d'affecter le menu */
        .form-wrapper {
            padding: 60px 15px;
            min-height: calc(100vh - 80px); /* laisse la place au menu */
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .form-container {
            width: 100%;
            max-width: 600px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .form-container h2 {
            text-align: center;
            color: #4B0082;
            margin-bottom: 25px;
        }

        /* Styles uniquement pour les inputs / textarea / buttons du formulaire */
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="file"],
        .form-container textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-container input:focus,
        .form-container textarea:focus {
            border-color: #6a5acd;
            box-shadow: 0 0 8px rgba(106, 90, 205, 0.3);
            outline: none;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        #btn-prix-ia {
            background: linear-gradient(135deg, #00bcd4, #ff6b81);
            color: #fff;
            margin-bottom: 10px;
        }

        #btn-prix-ia:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .form-container button[type="submit"] {
            background: linear-gradient(135deg, #6a5acd, #8a2be2);
            color: #fff;
        }

        .form-container button[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        #prix-ia-result {
            text-align: center;
            font-weight: 600;
            color: #ff6b81;
            font-size: 1.1rem;
        }

        @media (max-width: 768px){
            .form-wrapper {
                padding: 30px 10px;
            }
        }
    </style>
</head>
<body>

<!-- Formulaire Ajouter Logement -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Ajouter un logement</h2>
        <form id="form-logement" method="POST" enctype="multipart/form-data" action="ajouter_logement_action.php">
            <input type="text" name="titre" placeholder="Titre" required>
            <input type="text" name="ville" id="ville" placeholder="Ville" required>
            <input type="text" name="type_logement" id="type_logement" placeholder="Type de logement" required>
            <input type="number" name="capacite" id="capacite" placeholder="Capacité" required>
            <textarea name="equipements" id="equipements" placeholder="Équipements séparés par des virgules"></textarea>
            <input type="file" name="image" accept="image/*">
            <input type="number" step="0.01" name="prix_user" id="prix_user" placeholder="Prix que vous souhaitez" required>
            <!-- Champ caché pour stocker le prix IA -->
            <input type="hidden" name="prix_pred" id="prix_pred">
            <button type="button" id="btn-prix-ia">Calculer prix IA</button>
            <div id="prix-ia-result"></div>
            <button type="submit">Confirmer</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$("#btn-prix-ia").click(function(){
    let data = {
        ville: $("#ville").val(),
        type_logement: $("#type_logement").val(),
        capacite: parseInt($("#capacite").val()),
        equipements: $("#equipements").val()
    };

    $.ajax({
        url: "http://localhost:5000/predict",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function(response){
            // Afficher le prix IA
            $("#prix-ia-result").html("Prix suggéré par IA : <b>" + response.prix_pred + " MAD</b>");
            // Stocker le prix IA pour l'envoyer au backend
            $("#prix_pred").val(response.prix_pred);
        },
        error: function(){
            alert("Erreur lors de la récupération du prix IA.");
        }
    });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
