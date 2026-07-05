<?php
// menu.php
?>

<style>
/* Menu horizontal moderne avec dégradé bleu-rose pour contraster avec le mauve */
.navbar-custom {
    background: linear-gradient(135deg, #00bcd4, #ff6b81); /* bleu -> rose */
    padding: 0.8rem 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    font-family: 'Segoe UI', sans-serif;
}

.navbar-custom .navbar-brand {
    font-weight: 700;
    color: #fff;
    font-size: 1.5rem;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
}

/* Menu horizontal avec espace entre les liens */
.navbar-custom .navbar-nav {
    display: flex;           
    align-items: center;
    gap: 60px;               /* espace entre liens */
    margin-left: auto;
}

.navbar-custom .nav-link {
    color: #fff;
    font-weight: 500;
    padding: 8px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.navbar-custom .nav-link:hover {
    background-color: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}

.navbar-custom .nav-link.active {
    background-color: rgba(255,255,255,0.3);
    font-weight: 700;
    color: #fff;
}

.navbar-custom .btn-logout {
    background-color: #ff9800; /* bouton orange pour contraste */
    color: #fff;
    border-radius: 50px;
    padding: 6px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.navbar-custom .btn-logout:hover {
    background-color: #e65100;
    transform: translateY(-2px);
}

/* Responsive mobile */
@media (max-width: 768px) {
    .navbar-custom .navbar-nav {
        flex-direction: column; 
        gap: 15px;              
        margin-left: 0;
    }
    .navbar-custom .btn-logout {
        width: 100%;
        text-align: center;
    }
}
</style>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <span class="navbar-brand" style="color: black;">Homestay 2030</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" 
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='logements.php'?'active':'' ?>" href="logements.php">Logements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='ajouter_logement.php'?'active':'' ?>" href="ajouter_logement.php">Ajouter un logement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='mes_reservations.php'?'active':'' ?>" href="mes_reservations.php">Mes réservations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='profile.php'?'active':'' ?>" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-logout" href="deconnexion.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
