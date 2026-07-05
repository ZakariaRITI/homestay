<?php
require 'db.php';
require 'auth.php';
require_admin();

$action = $_POST['action'] ?? ($_GET['action'] ?? '');
$redirect = $_POST['redirect'] ?? ($_GET['redirect'] ?? 'admin_blocages.php');

function safe_redirect(string $url): void {
    if (!$url) $url = 'admin_blocages.php';
    // sécurité minimale: empêcher redirection externe
    if (preg_match('~^https?://~i', $url)) {
        $url = 'admin_blocages.php';
    }
    header('Location: ' . $url);
    exit();
}

function table_exists(PDO $pdo, string $table): bool {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool)$stmt->fetch();
    } catch (Exception $e) {
        return false;
    }
}

if (!table_exists($pdo, 'indisponibilites')) {
    $_SESSION['flash_error'] = "La table 'indisponibilites' n'existe pas. Exécute d'abord migration_blocage.sql.";
    safe_redirect($redirect);
}

if ($action === 'add') {
    $logement_id = (int)($_POST['logement_id'] ?? 0);
    $date_debut = trim($_POST['date_debut'] ?? '');
    $date_fin = trim($_POST['date_fin'] ?? '');
    $motif = trim($_POST['motif'] ?? '');

    if ($logement_id <= 0 || !$date_debut || !$date_fin) {
        $_SESSION['flash_error'] = "Veuillez remplir logement + dates.";
        safe_redirect($redirect);
    }
    if ($date_fin < $date_debut) {
        $_SESSION['flash_error'] = "La date de fin doit être après la date de début.";
        safe_redirect($redirect);
    }

    // Chevauchement blocages
    $check = $pdo->prepare(
        "SELECT COUNT(*) AS c
         FROM indisponibilites
         WHERE logement_id = ?
           AND NOT (date_fin < ? OR date_debut > ?)"
    );
    $check->execute([$logement_id, $date_debut, $date_fin]);
    $c = (int)($check->fetch()['c'] ?? 0);

    if ($c > 0) {
        $_SESSION['flash_error'] = "Ce logement a déjà une période bloquée qui chevauche ces dates.";
        safe_redirect($redirect);
    }

    $ins = $pdo->prepare("INSERT INTO indisponibilites (logement_id, date_debut, date_fin, motif) VALUES (?, ?, ?, ?)");
    $ins->execute([$logement_id, $date_debut, $date_fin, $motif ?: null]);

    $_SESSION['flash_success'] = "Période bloquée ajoutée avec succès.";
    safe_redirect($redirect);
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        $_SESSION['flash_error'] = "Blocage introuvable.";
        safe_redirect($redirect);
    }

    $del = $pdo->prepare("DELETE FROM indisponibilites WHERE id = ?");
    $del->execute([$id]);

    $_SESSION['flash_success'] = "Blocage supprimé.";
    safe_redirect($redirect);
}

$_SESSION['flash_error'] = "Action invalide.";
safe_redirect($redirect);
