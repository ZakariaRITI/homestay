<?php
require 'db.php';
require 'auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = $_GET['action'] ?? '';
$redirect = $_SERVER['HTTP_REFERER'] ?? 'admin_reservations.php';

if ($id <= 0 || !in_array($action, ['confirmer','annuler'], true)) {
    header('Location: ' . $redirect);
    exit();
}

$statut = ($action === 'confirmer') ? 'CONFIRMEE' : 'ANNULEE';

try {
    $stmt = $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $id]);
} catch (Exception $e) {
    // Si la colonne statut n'existe pas, on ne peut rien faire
}

header('Location: ' . $redirect);
exit();
