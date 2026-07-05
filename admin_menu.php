<?php
// admin_menu.php
?>

<style>
.navbar-custom {
    background: linear-gradient(135deg, #222, #6f42c1);
    padding: 0.8rem 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.35);
    font-family: 'Segoe UI', sans-serif;
}

.navbar-custom .navbar-brand {
    font-weight: 800;
    color: #fff;
    font-size: 1.4rem;
}

.navbar-custom .navbar-nav {
    display: flex;
    align-items: center;
    gap: 28px;
    margin-left: auto;
}

.navbar-custom .nav-link {
    color: #fff;
    font-weight: 500;
    padding: 8px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
}

.navbar-custom .nav-link:hover {
    background-color: rgba(255,255,255,0.15);
    transform: translateY(-2px);
}

.navbar-custom .nav-link.active {
    background-color: rgba(255,255,255,0.25);
    font-weight: 700;
}

.navbar-custom .btn-logout {
    background-color: #ff9800;
    color: #fff;
    border-radius: 50px;
    padding: 6px 20px;
    font-weight: 700;
    transition: all 0.3s ease;
    text-decoration: none;
}

.navbar-custom .btn-logout:hover {
    background-color: #e65100;
    transform: translateY(-2px);
}

@media (max-width: 992px) {
    .navbar-custom .navbar-nav {
        flex-direction: column;
        gap: 12px;
        margin-left: 0;
        align-items: stretch;
    }
    .navbar-custom .btn-logout {
        width: 100%;
        text-align: center;
        display: inline-block;
    }
}
</style>

<nav class="navbar navbar-expand-lg navbar-custom mb-3">
    <div class="container">
        <span class="navbar-brand">Homestay 2030 - Admin</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" 
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='admin_dashboard.php'?'active':'' ?>" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='admin_logements.php'?'active':'' ?>" href="admin_logements.php">Logements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='admin_reservations.php'?'active':'' ?>" href="admin_reservations.php">Réservations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='admin_blocages.php'?'active':'' ?>" href="admin_blocages.php">Blocages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='admin_users.php'?'active':'' ?>" href="admin_users.php">Utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='profile.php'?'active':'' ?>" href="profile2.php">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="btn-logout" href="deconnexion.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
