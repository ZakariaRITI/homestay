<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = 'ADMIN' WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $_SESSION['role'] = 'ADMIN';
    echo "<h1>Promoted to ADMIN</h1><a href='admin_dashboard.php'>Go to Dashboard</a>";
}
else {
    echo "<h1>Not logged in</h1><a href='login.php'>Login first</a>";
}
?>
