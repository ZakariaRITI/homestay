<?php
// auth.php : helpers de sécurité (session + rôle)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_role(): string {
    return $_SESSION['role'] ?? '';
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit();
    }
}

function require_admin(): void {
    require_login();
    if (current_role() !== 'ADMIN') {
        header('Location: logements.php');
        exit();
    }
}

function require_user(): void {
    require_login();
    // Autorise USER **et** ADMIN à accéder aux pages "côté utilisateur" (ex: consulter et louer).
    // L'espace Admin reste protégé via require_admin().
    if (!in_array(current_role(), ['USER', 'ADMIN'], true)) {
        header('Location: index.php');
        exit();
    }
}
