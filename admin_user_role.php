<?php
require 'db.php';
require 'auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$role = $_GET['role'] ?? '';

if ($id <= 0 || !in_array($role, ['USER','ADMIN'], true)) {
    header('Location: admin_users.php');
    exit();
}

// Empêcher de se retirer soi-même les droits admin
if ($id === (int)($_SESSION['user_id'] ?? 0) && $role !== 'ADMIN') {
    header('Location: admin_users.php');
    exit();
}

$stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$role, $id]);

header('Location: admin_users.php');
exit();
