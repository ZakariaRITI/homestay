<?php
require 'db.php';
require 'auth.php';
require_admin();

include 'admin_menu.php';

// Helpers
function table_column_exists(PDO $pdo, string $table, string $column): bool {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return (bool)$stmt->fetch();
    } catch (Exception $e) {
        return false;
    }
}

$has_statut = table_column_exists($pdo, 'reservations', 'statut');

// Filters
$statut = trim($_GET['statut'] ?? ''); // EN_ATTENTE/CONFIRMEE/ANNULEE
$logement_id_filter = (int)($_GET['logement_id'] ?? 0);
$user_id_filter = (int)($_GET['user_id'] ?? 0);
$search = trim($_GET['q'] ?? '');

// Pagination
$per_page = 10;
$page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($page - 1) * $per_page;

// Dropdown lists
$logements_list = [];
$users_list = [];
try {
    $logements_list = $pdo->query("SELECT id, titre, ville FROM logements ORDER BY titre")->fetchAll();
} catch (Exception $e) {}
try {
    $users_list = $pdo->query("SELECT id, nom, email FROM users ORDER BY nom")->fetchAll();
} catch (Exception $e) {}

// Build WHERE
$where = [];
$params = [];
if ($logement_id_filter > 0) {
    $where[] = 'r.logement_id = ?';
    $params[] = $logement_id_filter;
}
if ($user_id_filter > 0) {
    $where[] = 'r.user_id = ?';
    $params[] = $user_id_filter;
}
if ($has_statut && $statut !== '') {
    $where[] = 'r.statut = ?';
    $params[] = $statut;
}
if ($search !== '') {
    $where[] = '(u.nom LIKE ? OR u.email LIKE ? OR l.titre LIKE ? OR l.ville LIKE ? OR r.id LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like, $like);
}

$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Count
$total_rows = 0;
try {
    $count_sql = "SELECT COUNT(*) AS c
                  FROM reservations r
                  JOIN users u ON u.id = r.user_id
                  JOIN logements l ON l.id = r.logement_id
                  $where_sql";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_rows = (int)($stmt->fetch()['c'] ?? 0);
} catch (Exception $e) {
    $total_rows = 0;
}
$total_pages = max(1, (int)ceil($total_rows / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $per_page;
}

// Fetch
$reservations = [];
try {
    $sql = "SELECT r.*, u.nom AS client, u.email AS client_email,
                   l.titre AS logement_titre, l.ville AS logement_ville
            FROM reservations r
            JOIN users u ON u.id = r.user_id
            JOIN logements l ON l.id = r.logement_id
            $where_sql
            ORDER BY r.created_at DESC
            LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll();
} catch (Exception $e) {
    $reservations = [];
}

function build_query(array $overrides = []): string {
    $q = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null || $v === '') {
            unset($q[$k]);
        } else {
            $q[$k] = $v;
        }
    }
    return http_build_query($q);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Réservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1c1c1c, #6f42c1); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 20px; background-color: rgba(0,0,0,0.70); color: #fff; box-shadow: 0 8px 25px rgba(0,0,0,0.45); }
        .table thead th { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.15); }
        .table td { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.10); }
        .muted { color: rgba(255,255,255,0.75); }
        .form-label { color: rgba(255,255,255,0.85); }
        .form-control, .form-select { background-color: rgba(255,255,255,0.10); color: #fff; border: 1px solid rgba(255,255,255,0.18); }
        .form-control::placeholder { color: rgba(255,255,255,0.55); }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.2rem rgba(111,66,193,0.35); border-color: rgba(111,66,193,0.7); }
        option { color: #000; }
        .pagination .page-link { background-color: rgba(255,255,255,0.10); border-color: rgba(255,255,255,0.18); color: #fff; }
        .pagination .page-link:hover { background-color: rgba(255,255,255,0.18); }
        .pagination .page-item.active .page-link { background-color: rgba(111,66,193,0.7); border-color: rgba(111,66,193,0.7); }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="m-0">Gestion des réservations</h3>
            <span class="muted">Confirmer / Annuler</span>
        </div>

        <form class="row g-2 align-items-end mb-3" method="GET">
            <div class="col-md-3">
                <label class="form-label">Logement</label>
                <select class="form-select" name="logement_id">
                    <option value="0">Tous</option>
                    <?php foreach ($logements_list as $l): ?>
                        <option value="<?= (int)$l['id'] ?>" <?= $logement_id_filter===(int)$l['id']?'selected':'' ?>>
                            <?= htmlspecialchars(($l['titre'] ?? '') . ' — ' . ($l['ville'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Utilisateur</label>
                <select class="form-select" name="user_id">
                    <option value="0">Tous</option>
                    <?php foreach ($users_list as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= $user_id_filter===(int)$u['id']?'selected':'' ?>>
                            <?= htmlspecialchars(($u['nom'] ?? '') . ' — ' . ($u['email'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select class="form-select" name="statut" <?= $has_statut ? '' : 'disabled' ?>>
                    <option value="">Tous</option>
                    <option value="EN_ATTENTE" <?= $statut==='EN_ATTENTE'?'selected':'' ?>>EN ATTENTE</option>
                    <option value="CONFIRMEE" <?= $statut==='CONFIRMEE'?'selected':'' ?>>CONFIRMÉE</option>
                    <option value="ANNULEE" <?= $statut==='ANNULEE'?'selected':'' ?>>ANNULÉE</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Recherche</label>
                <input class="form-control" type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="client, logement, ville...">
            </div>

            <div class="col-md-1 d-grid">
                <button class="btn btn-outline-light" type="submit">OK</button>
            </div>

            <div class="col-12">
                <input class="form-control" id="liveSearch" type="text" placeholder="Recherche instantanée (dans le tableau affiché)...">
            </div>

            <?php if (!$has_statut): ?>
                <div class="col-12 muted" style="font-size:0.9rem">
                    ⚠️ Le filtre par statut est désactivé : exécute <b>migration_admin.sql</b> pour ajouter la colonne <code>statut</code>.
                </div>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle" id="dataTable">
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
                        <?php $st = $has_statut ? ($r['statut'] ?? 'EN_ATTENTE') : 'EN_ATTENTE'; ?>
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
                                <?php if ($st === 'CONFIRMEE'): ?>
                                    <span class="badge bg-success">CONFIRMÉE</span>
                                <?php elseif ($st === 'ANNULEE'): ?>
                                    <span class="badge bg-danger">ANNULÉE</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">EN ATTENTE</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-grid gap-1">
                                    <a class="btn btn-sm btn-success" href="admin_reservation_action.php?id=<?= (int)$r['id'] ?>&action=confirmer&redirect=<?= urlencode('admin_reservations.php?' . build_query()) ?>">Confirmer</a>
                                    <a class="btn btn-sm btn-outline-danger" href="admin_reservation_action.php?id=<?= (int)$r['id'] ?>&action=annuler&redirect=<?= urlencode('admin_reservations.php?' . build_query()) ?>">Annuler</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="muted" style="font-size:0.9rem">
                <?= (int)$total_rows ?> résultat(s)
            </div>

            <nav aria-label="pagination">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page<=1?'disabled':'' ?>">
                        <a class="page-link" href="admin_reservations.php?<?= htmlspecialchars(build_query(['p' => max(1,$page-1)])) ?>">‹</a>
                    </li>
                    <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i=$start; $i<=$end; $i++):
                    ?>
                        <li class="page-item <?= $i===$page?'active':'' ?>">
                            <a class="page-link" href="admin_reservations.php?<?= htmlspecialchars(build_query(['p' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page>=$total_pages?'disabled':'' ?>">
                        <a class="page-link" href="admin_reservations.php?<?= htmlspecialchars(build_query(['p' => min($total_pages,$page+1)])) ?>">›</a>
                    </li>
                </ul>
            </nav>
        </div>

        
    </div>
</div>

<script>
// Recherche instantanée (client-side sur la page courante)
(function () {
  const input = document.getElementById('liveSearch');
  const table = document.getElementById('dataTable');
  if (!input || !table) return;
  input.addEventListener('input', function () {
    const q = input.value.toLowerCase();
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(r => {
      const text = r.innerText.toLowerCase();
      r.style.display = text.includes(q) ? '' : 'none';
    });
  });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
