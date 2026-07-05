<?php
session_start();
require_once "db.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $titre = $_POST['titre'];
    $ville = $_POST['ville'];
    $type_logement = $_POST['type_logement'];
    $capacite = $_POST['capacite'];
    $equipements = $_POST['equipements'];
    $prix_user = $_POST['prix_user'];
    $prix_pred = $_POST['prix_pred']; // récupéré depuis le champ caché

   $image = '';
$image_dir = ''; // image ou images

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

    $image_dir = "image"; // dossier pour les users
    $target_dir = $image_dir . "/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $image = uniqid('logement_', true) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
    }
}



    $_SESSION['logement_temp'] = [
    'user_id' => $_SESSION['user_id'],
    'titre' => $titre,
    'ville' => $ville,
    'type_logement' => $type_logement,
    'capacite' => $capacite,
    'equipements' => $equipements,
    'image' => $image,
    'image_dir' => $image_dir, // 👈 NOUVEAU
    'prix_user' => $prix_user,
    'prix_pred' => $prix_pred
];


    // Redirection vers la confirmation
    header("Location: confirmer_prix.php");
    exit();
} else {
    header("Location: ajouter_logement.php");
    exit();
}
?>
