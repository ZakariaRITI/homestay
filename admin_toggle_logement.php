<?php
require 'db.php';
require 'auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$redirect = $_SERVER['HTTP_REFERER'] ?? 'admin_logements.php';

if ($id <= 0) {
    header('Location: ' . $redirect);
    exit();
}

// Toggle disponible
try {
    $stmt = $pdo->prepare("UPDATE logements SET disponible = IF(disponible = 1, 0, 1) WHERE id = ?");
    $stmt->execute([$id]);
} catch (Exception $e) {
    // Si la colonne n'existe pas, on ne peut rien faire
}

header('Location: ' . $redirect);
exit();
