<?php
require 'db.php';

$message = strtolower(trim($_POST['message'] ?? ''));

/* =========================
   FONCTION UTILITAIRE
   ========================= */
function contains($words, $message) {
    foreach ($words as $word) {
        if (strpos($message, $word) !== false) {
            return true;
        }
    }
    return false;
}

/* =========================
   LISTE DES VILLES
   ========================= */
$villes = [
    "casablanca", "rabat", "marrakech", "tanger",
    "agadir", "fes", "fès", "meknes", "oujda"
];

/* =========================
   DETECTION DES VILLES
   ========================= */
foreach ($villes as $ville) {
    if (strpos($message, $ville) !== false) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM logements WHERE ville LIKE ?");
        $stmt->execute(["%$ville%"]);
        $count = $stmt->fetchColumn();

        echo "🏙️ Il y a actuellement $count logements disponibles à " . ucfirst($ville) . 
             ". Utilisez les filtres pour les afficher.";
        exit;
    }
}

/* =========================
   INTENTIONS DU CHATBOT
   ========================= */
$intents = [

    "salutation" => [
        "keywords" => ["bonjour", "salut", "hello", "bonsoir", "hey"],
        "response" => "Bonjour 👋 Je suis l’assistant Homestay 2030. Comment puis-je vous aider ?"
    ],

    "logement" => [
        "keywords" => ["logement", "hébergement", "maison", "appartement", "villa", "riad", "dormir"],
        "response" => "🏠 Nous proposons plusieurs types de logements dans différentes villes du Maroc."
    ],

    "reservation" => [
        "keywords" => ["réserver", "reservation", "book", "booking", "louer", "confirmer"],
        "response" => "📅 Pour réserver, choisissez un logement puis cliquez sur le bouton Réserver."
    ],

    "prix" => [
        "keywords" => ["prix", "cher", "pas cher", "budget", "coût", "combien", "tarif", "mad", "dh"],
        "response" => "💰 Les prix varient selon la ville, le type de logement et la période du séjour."
    ],

    "capacite" => [
        "keywords" => ["capacité", "personnes", "famille", "groupe", "amis"],
        "response" => "👥 Chaque logement précise le nombre maximum de personnes acceptées."
    ],

    "annulation" => [
        "keywords" => ["annuler", "annulation", "cancel", "modifier", "remboursement"],
        "response" => "❌ Vous pouvez annuler une réservation depuis votre espace personnel."
    ],

    "avis" => [
        "keywords" => ["avis", "note", "étoiles", "commentaire", "feedback"],
        "response" => "⭐ Les utilisateurs peuvent laisser un avis après leur séjour."
    ],

    "stade" => [
        "keywords" => ["stade", "match", "football"],
        "response" => "⚽ Vous pouvez trouver des logements proches des stades de la Coupe du Monde 2030."
    ],

    "worldcup" => [
        "keywords" => ["coupe du monde", "world cup", "2030", "mondial"],
        "response" => "🇲🇦⚽ Homestay 2030 est conçu spécialement pour les supporters de la Coupe du Monde."
    ],

    "paiement" => [
        "keywords" => ["paiement", "payer", "carte", "cash", "facture"],
        "response" => "💳 Le paiement peut se faire en ligne ou à l’arrivée selon le logement."
    ],

    "contact" => [
        "keywords" => ["contact", "support", "aide", "help", "problème"],
        "response" => "📞 Vous pouvez contacter le support via la page Contact."
    ]
];

/* =========================
   ANALYSE DES INTENTIONS
   ========================= */
foreach ($intents as $intent) {
    if (contains($intent['keywords'], $message)) {
        echo $intent['response'];
        exit;
    }
}

/* =========================
   REPONSE PAR DEFAUT
   ========================= */
echo "🤖 Je n’ai pas bien compris. Pouvez-vous préciser ?
(Ex : prix, réservation, Casablanca, stade, logement)";
