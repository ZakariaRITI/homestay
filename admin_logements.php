<?php
require 'db.php';
require 'auth.php';
require_admin();

include 'admin_menu.php';

// Helpers
function table_exists(PDO $pdo, string $table): bool {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool)$stmt->fetch();
    } catch (Exception $e) {
        return false;
    }
}

// Filters
$ville = trim($_GET['ville'] ?? '');
$dispo = $_GET['dispo'] ?? 'all'; // all | 1 | 0
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$has_disponible = true;
try {
    $pdo->query("SELECT disponible FROM logements LIMIT 1");
} catch (Exception $e) {
    $has_disponible = false;
}

$has_indispo = table_exists($pdo, 'indisponibilites');

// Villes list
$villes = [];
try {
    $stmtV = $pdo->query("SELECT DISTINCT ville FROM logements ORDER BY ville ASC");
    $villes = $stmtV->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

$where = [];
$params = [];

if ($ville !== '') {
    $where[] = 'l.ville = ?';
    $params[] = $ville;
}
if ($has_disponible && ($dispo === '1' || $dispo === '0')) {
    $where[] = 'l.disponible = ?';
    $params[] = (int)$dispo;
}
if ($q !== '') {
    $where[] = '(l.titre LIKE ? OR l.ville LIKE ? OR u.nom LIKE ? OR u.email LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like);
}

$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Count
$total = 0;
try {
    $stmtC = $pdo->prepare(
        "SELECT COUNT(*) AS c
         FROM logements l
         JOIN users u ON u.id = l.user_id
         $where_sql"
    );
    $stmtC->execute($params);
    $total = (int)($stmtC->fetch()['c'] ?? 0);
} catch (Exception $e) {
    $total = 0;
}

$pages = max(1, (int)ceil($total / $per_page));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $per_page; }

// Data
$logements = [];
try {
    if ($has_indispo) {
        $sql =
            "SELECT l.*, u.nom AS proprietaire, u.email AS proprietaire_email,
                    (SELECT COUNT(*) FROM indisponibilites i WHERE i.logement_id = l.id) AS nb_blocages
             FROM logements l
             JOIN users u ON u.id = l.user_id
             $where_sql
             ORDER BY l.created_at DESC
             LIMIT $per_page OFFSET $offset";
    } else {
        $sql =
            "SELECT l.*, u.nom AS proprietaire, u.email AS proprietaire_email,
                    0 AS nb_blocages
             FROM logements l
             JOIN users u ON u.id = l.user_id
             $where_sql
             ORDER BY l.created_at DESC
             LIMIT $per_page OFFSET $offset";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logements = $stmt->fetchAll();
} catch (Exception $e) {
    $logements = [];
}

// Build query string for pagination links
function build_qs(array $extra = []): string {
    $base = $_GET;
    foreach ($extra as $k => $v) {
        $base[$k] = $v;
    }
    return http_build_query($base);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Logements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1c1c1c, #6f42c1); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 20px; background-color: rgba(0,0,0,0.70); color: #fff; box-shadow: 0 8px 25px rgba(0,0,0,0.45); }
        .table thead th { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.15); }
        .table td { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.10); }
        .muted { color: rgba(255,255,255,0.75); }
        .form-control, .form-select { background-color: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.15); }
        .form-control:focus, .form-select:focus { border-color: rgba(255,255,255,0.35); box-shadow: none; }
        .form-select option { color: #111; }
        .pagination .page-link { background-color: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.15); }
        .pagination .page-item.active .page-link { background-color: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.25); }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="card p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h3 class="m-0">Gestion des logements</h3>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-light" href="admin_blocages.php">Blocages (calendrier)</a>
                <a class="btn btn-sm btn-outline-light" href="ajouter_logement.php">+ Ajouter</a>
            </div>
        </div>

        <form class="row g-2 mb-3" method="GET">
            <div class="col-md-3">
                <label class="form-label muted">Ville</label>
                <select class="form-select" name="ville">
                    <option value="">Toutes</option>
                    <?php foreach ($villes as $v): ?>
                        <option value="<?= htmlspecialchars($v) ?>" <?= ($ville === $v) ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label muted">Disponibilité</label>
                <select class="form-select" name="dispo" <?= $has_disponible ? '' : 'disabled' ?>>
                    <option value="all" <?= ($dispo === 'all') ? 'selected' : '' ?>>Toutes</option>
                    <option value="1" <?= ($dispo === '1') ? 'selected' : '' ?>>Disponible</option>
                    <option value="0" <?= ($dispo === '0') ? 'selected' : '' ?>>Indisponible</option>
                </select>
                <?php if (!$has_disponible): ?>
                    <div class="muted" style="font-size:0.85rem">(Exécute <b>migration_admin.sql</b> pour activer ce filtre)</div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label muted">Recherche (serveur)</label>
                <input class="form-control" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Titre, ville, propriétaire...">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button class="btn btn-light w-100" type="submit">Filtrer</button>
                <a class="btn btn-outline-light" href="admin_logements.php">Reset</a>
            </div>
        </form>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div class="muted">Total: <b><?= (int)$total ?></b> logements</div>
            <div class="col-md-4" style="max-width: 360px;">
                <input id="liveSearch" class="form-control" type="text" placeholder="Recherche instantanée (sur la page)...">
                <div class="muted" style="font-size:0.85rem">Filtre instantanément les lignes visibles (sans recharger)</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle" id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Ville</th>
                        <th>Propriétaire</th>
                        <th>Prix</th>
                        <th>Blocages</th>
                        <th>Disponibilité</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($logements)): ?>
                    <tr><td colspan="8">Aucun logement.</td></tr>
                <?php else: ?>
                    <?php foreach ($logements as $l): ?>
                        <?php
                            $dispo_val = 1;
                            if (array_key_exists('disponible', $l)) {
                                $dispo_val = (int)$l['disponible'];
                            }
                            $nb_blocages = (int)($l['nb_blocages'] ?? 0);
                        ?>
                        <tr>
                            <td><?= (int)$l['id'] ?></td>
                            <td><?= htmlspecialchars($l['titre'] ?? '') ?></td>
                            <td><?= htmlspecialchars($l['ville'] ?? '') ?></td>
                            <td>
                                <div><?= htmlspecialchars($l['proprietaire'] ?? '') ?></div>
                                <div class="muted" style="font-size:0.85rem"><?= htmlspecialchars($l['proprietaire_email'] ?? '') ?></div>
                            </td>
                            <td><?= htmlspecialchars($l['prix'] ?? '') ?></td>
                            <td>
                                <?php if ($has_indispo): ?>
                                    <span class="badge bg-info text-dark"><?= $nb_blocages ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($dispo_val === 1): ?>
                                    <span class="badge bg-success">DISPONIBLE</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">INDISPONIBLE</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a class="btn btn-sm btn-warning" href="admin_toggle_logement.php?id=<?= (int)$l['id'] ?>&redirect=<?= urlencode('admin_logements.php?' . build_qs()) ?>">Basculer</a>
                                    <a class="btn btn-sm btn-outline-light" href="admin_blocages.php?logement_id=<?= (int)$l['id'] ?>">Gérer blocages</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <nav class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin_logements.php?<?= build_qs(['page' => max(1, $page-1)]) ?>">&laquo;</a>
                </li>
                <?php
                    $start = max(1, $page - 2);
                    $end = min($pages, $page + 2);
                    for ($i = $start; $i <= $end; $i++):
                ?>
                    <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="admin_logements.php?<?= build_qs(['page' => $i]) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin_logements.php?<?= build_qs(['page' => min($pages, $page+1)]) ?>">&raquo;</a>
                </li>
            </ul>
        </nav>

        
    </div>
</div>

<script>
(function() {
    const input = document.getElementById('liveSearch');
    const table = document.getElementById('dataTable');
    if (!input || !table) return;

    input.addEventListener('input', function() {
        const term = (input.value || '').toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row) => {
            const txt = row.innerText.toLowerCase();
            row.style.display = txt.includes(term) ? '' : 'none';
        });
    });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
