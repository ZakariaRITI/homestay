<?php
require 'db.php';
include 'menu.php';

/* ======================
   FILTRAGE DES LOGEMENTS
   ====================== */
$sql = "SELECT * FROM logements WHERE 1=1";
$params = [];

if (!empty($_GET['ville'])) {
    $sql .= " AND ville LIKE ?";
    $params[] = "%" . $_GET['ville'] . "%";
}

if (!empty($_GET['type'])) {
    $sql .= " AND type_logement = ?";
    $params[] = $_GET['type'];
}

if (!empty($_GET['capacite'])) {
    $sql .= " AND capacite >= ?";
    $params[] = $_GET['capacite'];
}

if (!empty($_GET['prix_max'])) {
    $sql .= " AND prix <= ?";
    $params[] = $_GET['prix_max'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logements = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Logements Homestay 2030</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f5f6f8;
    min-height: 100vh;
    font-family: 'Segoe UI', sans-serif;
    display: flex;
    flex-direction: column;
}

h2 {
    color: #2c2c2c;
    font-weight: 600;
}

.card-logement {
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 30px;
    background-color: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.card-logement:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.card-logement img {
    height: 220px;
    object-fit: cover;
    width: 100%;
}

.btn-book {
    background-color: #0d6efd;
    border: none;
    font-weight: 600;
    color: #fff;
    border-radius: 6px;
}

footer {
    background: linear-gradient(135deg, #00bcd4, #ff6b81);
    color: #fff;
    padding: 40px 0;
    margin-top: auto;
}
</style>
</head>
<!-- ===== CHATBOT ===== -->
<div id="chatbot">
    <div id="chatbot-header">🤖 Assistant Homestay 2030</div>

    <div id="chatbot-body"></div>

    <div id="chatbot-input">
        <input type="text" id="userMessage" placeholder="Posez votre question...">
        <button onclick="sendMessage()">Envoyer</button>
    </div>
</div>

<button id="chatbot-toggle" onclick="toggleChatbot()">💬</button>

<style>
#chatbot {
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 320px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 9999;
}

#chatbot-header {
    background: #0d6efd;
    color: white;
    padding: 12px;
    font-weight: bold;
    text-align: center;
}

#chatbot-body {
    height: 260px;
    padding: 10px;
    overflow-y: auto;
    font-size: 14px;
}

#chatbot-body p {
    margin: 5px 0;
}

.user { color: #0d6efd; font-weight: bold; }
.bot { color: #333; }

#chatbot-input {
    display: flex;
    border-top: 1px solid #ddd;
}

#chatbot-input input {
    flex: 1;
    border: none;
    padding: 10px;
}

#chatbot-input button {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 10px 15px;
}

#chatbot-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #0d6efd;
    color: white;
    border: none;
    border-radius: 50%;
    width: 55px;
    height: 55px;
    font-size: 22px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.3);
}
</style>
<script>
function toggleChatbot() {
    const bot = document.getElementById("chatbot");
    bot.style.display = bot.style.display === "flex" ? "none" : "flex";
}

function sendMessage() {
    let message = document.getElementById("userMessage").value;
    if(message.trim() === "") return;

    let body = document.getElementById("chatbot-body");
    body.innerHTML += `<p class="user">Vous : ${message}</p>`;

    fetch("chatbot.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "message=" + encodeURIComponent(message)
    })
    .then(res => res.text())
    .then(response => {
        body.innerHTML += `<p class="bot">Bot : ${response}</p>`;
        body.scrollTop = body.scrollHeight;
    });

    document.getElementById("userMessage").value = "";
}
</script>

<body>

<div class="container py-5">

    <h2 class="text-center mb-4">Nos logements disponibles</h2>

    <!-- =========================
         FORMULAIRE DE FILTRAGE
         ========================= -->
    <form method="GET" class="row g-3 mb-5 bg-white p-4 rounded shadow-sm">

        <div class="col-md-3">
            <input type="text" name="ville" class="form-control"
                   placeholder="Ville"
                   value="<?= $_GET['ville'] ?? '' ?>">
        </div>

        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">Type de logement</option>
                <option value="Appartement" <?= (($_GET['type'] ?? '')=='Appartement')?'selected':'' ?>>Appartement</option>
                <option value="Maison" <?= (($_GET['type'] ?? '')=='Maison')?'selected':'' ?>>Maison</option>
                <option value="Villa" <?= (($_GET['type'] ?? '')=='Villa')?'selected':'' ?>>Villa</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="number" name="capacite" class="form-control"
                   placeholder="Capacité min"
                   value="<?= $_GET['capacite'] ?? '' ?>">
        </div>

        <div class="col-md-2">
            <input type="number" name="prix_max" class="form-control"
                   placeholder="Prix max (MAD)"
                   value="<?= $_GET['prix_max'] ?? '' ?>">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrer</button>
        </div>
    </form>

    <!-- ======================
         LISTE DES LOGEMENTS
         ====================== -->
    <div class="row">

    <?php
    if (count($logements) === 0) {
        echo '<div class="col-12 text-center text-muted">
                <h5>Aucun logement ne correspond à votre recherche.</h5>
              </div>';
    }

    foreach ($logements as $logement) {
        echo '
        <div class="col-md-4">
            <div class="card card-logement">
                <img src="image/'.$logement['image'].'" alt="'.$logement['titre'].'">
                <div class="card-body">
                    <h5>'.$logement['titre'].'</h5>
                    <p>Ville : '.$logement['ville'].'</p>
                    <p>Type : '.$logement['type_logement'].'</p>
                    <p>Capacité : '.$logement['capacite'].' personnes</p>
                    <p>Prix : '.$logement['prix'].' MAD / nuit</p>
                    <a href="reserver.php?logement_id='.$logement['id'].'" class="btn btn-book w-100">Réserver</a>
                </div>
            </div>
        </div>';
    }
    ?>

    </div>
</div>

<footer class="text-center">
    <p>&copy; <?= date('Y') ?> Homestay 2030 - Tous droits réservés</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
