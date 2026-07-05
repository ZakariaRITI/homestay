<?php
require 'db.php';
require 'auth.php';
require_admin();

include 'admin_menu.php';

// Stats simples
$stats = [
    'users' => 0,
    'logements' => 0,
    'logements_dispo' => 0,
    'reservations' => 0,
    'reservations_attente' => 0
];

try {
    $stats['users'] = (int)$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
    $stats['logements'] = (int)$pdo->query("SELECT COUNT(*) AS c FROM logements")->fetch()['c'];
    // si champ disponible non encore migré, fallback
    try {
        $stats['logements_dispo'] = (int)$pdo->query("SELECT COUNT(*) AS c FROM logements WHERE disponible = 1")->fetch()['c'];
    } catch (Exception $e) {
        $stats['logements_dispo'] = $stats['logements'];
    }
    $stats['reservations'] = (int)$pdo->query("SELECT COUNT(*) AS c FROM reservations")->fetch()['c'];
    try {
        $stats['reservations_attente'] = (int)$pdo->query("SELECT COUNT(*) AS c FROM reservations WHERE statut = 'EN_ATTENTE'")->fetch()['c'];
    } catch (Exception $e) {
        $stats['reservations_attente'] = 0;
    }
} catch (Exception $e) {
    // rien
}

// Récupérer logements (avec l'info propriétaire)
$logements = [];
try {
    $stmt = $pdo->query(
        "SELECT l.*, u.nom AS proprietaire
         FROM logements l
         JOIN users u ON u.id = l.user_id
         ORDER BY l.created_at DESC"
    );
    $logements = $stmt->fetchAll();
} catch (Exception $e) {}

// Récupérer réservations
$reservations = [];
try {
    $stmt = $pdo->query(
        "SELECT r.*, u.nom AS client, u.email AS client_email,
                l.titre AS logement_titre, l.ville AS logement_ville
         FROM reservations r
         JOIN users u ON u.id = r.user_id
         JOIN logements l ON l.id = r.logement_id
         ORDER BY r.created_at DESC"
    );
    $reservations = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1c1c1c, #6f42c1);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 20px;
            background-color: rgba(0,0,0,0.70);
            color: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.45);
        }
        .badge-soft {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.20);
        }
        .table thead th {
            color: rgba(255,255,255,0.85);
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        .table td {
            color: rgba(255,255,255,0.85);
            border-bottom: 1px solid rgba(255,255,255,0.10);
        }
        .table {
            margin-bottom: 0;
        }
        .muted {
            color: rgba(255,255,255,0.75);
        }
    </style>
</head>
<body>
<div class="container py-4">

    <div class="row g-3 mb-3">
        <div class="col-md-2">
            <div class="card p-3">
                <div class="muted">Utilisateurs</div>
                <div class="fs-3 fw-bold"><?= (int)$stats['users'] ?></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3">
                <div class="muted">Logements</div>
                <div class="fs-3 fw-bold"><?= (int)$stats['logements'] ?></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3">
                <div class="muted">Disponibles</div>
                <div class="fs-3 fw-bold"><?= (int)$stats['logements_dispo'] ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="muted">Réservations</div>
                <div class="fs-3 fw-bold"><?= (int)$stats['reservations'] ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="muted">En attente</div>
                <div class="fs-3 fw-bold"><?= (int)$stats['reservations_attente'] ?></div>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-light" href="admin_logements.php">Gestion logements</a>
            <a class="btn btn-outline-light" href="admin_reservations.php">Gestion réservations</a>
            <a class="btn btn-outline-light" href="admin_blocages.php">Blocages calendrier</a>
            <a class="btn btn-outline-light" href="admin_users.php">Utilisateurs</a>
        </div>
        
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="m-0">Gestion des logements</h4>
                    <span class="badge badge-soft">Activer/Désactiver</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Ville</th>
                                <th>Propriétaire</th>
                                <th>Prix</th>
                                <th>Disponibilité</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($logements)): ?>
                            <tr><td colspan="7">Aucun logement.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logements as $l): ?>
                                <?php
                                    $dispo = 1;
                                    if (array_key_exists('disponible', $l)) {
                                        $dispo = (int)$l['disponible'];
                                    }
                                ?>
                                <tr>
                                    <td><?= (int)$l['id'] ?></td>
                                    <td><?= htmlspecialchars($l['titre'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($l['ville'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($l['proprietaire'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($l['prix'] ?? '') ?></td>
                                    <td>
                                        <?php if ($dispo === 1): ?>
                                            <span class="badge bg-success">DISPONIBLE</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">INDISPONIBLE</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-warning" href="admin_toggle_logement.php?id=<?= (int)$l['id'] ?>">
                                            Basculer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="m-0">Gestion des réservations</h4>
                    <span class="badge badge-soft">Confirmer/Annuler</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Logement</th>
                                <th>Dates</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($reservations)): ?>
                            <tr><td colspan="6">Aucune réservation.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $r): ?>
                                <?php $statut = $r['statut'] ?? 'EN_ATTENTE'; ?>
                                <tr>
                                    <td><?= (int)$r['id'] ?></td>
                                    <td>
                                        <div><?= htmlspecialchars($r['client'] ?? '') ?></div>
                                        <div class="muted" style="font-size:0.85rem"><?= htmlspecialchars($r['client_email'] ?? '') ?></div>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($r['logement_titre'] ?? '') ?></div>
                                        <div class="muted" style="font-size:0.85rem"><?= htmlspecialchars($r['logement_ville'] ?? '') ?></div>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($r['date_debut'] ?? '') ?></div>
                                        <div><?= htmlspecialchars($r['date_fin'] ?? '') ?></div>
                                    </td>
                                    <td>
                                        <?php if ($statut === 'CONFIRMEE'): ?>
                                            <span class="badge bg-success">CONFIRMÉE</span>
                                        <?php elseif ($statut === 'ANNULEE'): ?>
                                            <span class="badge bg-danger">ANNULÉE</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">EN ATTENTE</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-grid gap-1">
                                            <a class="btn btn-sm btn-success" href="admin_reservation_action.php?id=<?= (int)$r['id'] ?>&action=confirmer">Confirmer</a>
                                            <a class="btn btn-sm btn-outline-danger" href="admin_reservation_action.php?id=<?= (int)$r['id'] ?>&action=annuler">Annuler</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
