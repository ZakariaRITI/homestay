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

$has_table = table_exists($pdo, 'indisponibilites');

// Fetch logements for dropdown
$logements = [];
try {
    $logements = $pdo->query("SELECT id, titre, ville FROM logements ORDER BY ville, titre")->fetchAll();
} catch (Exception $e) {}

// Filters
$logement_id = isset($_GET['logement_id']) ? (int)$_GET['logement_id'] : 0;
$q = trim($_GET['q'] ?? '');
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$items = [];
$total = 0;
$error = null;

if ($has_table) {
    $where = [];
    $params = [];

    if ($logement_id > 0) {
        $where[] = 'i.logement_id = ?';
        $params[] = $logement_id;
    }
    if ($from !== '') {
        $where[] = 'i.date_fin >= ?';
        $params[] = $from;
    }
    if ($to !== '') {
        $where[] = 'i.date_debut <= ?';
        $params[] = $to;
    }
    if ($q !== '') {
        $where[] = '(l.titre LIKE ? OR l.ville LIKE ? OR i.motif LIKE ?)';
        $params[] = "%$q%";
        $params[] = "%$q%";
        $params[] = "%$q%";
    }

    $where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    try {
        $count = $pdo->prepare(
            "SELECT COUNT(*) AS c
             FROM indisponibilites i
             JOIN logements l ON l.id = i.logement_id
             $where_sql"
        );
        $count->execute($params);
        $total = (int)($count->fetch()['c'] ?? 0);

        $stmt = $pdo->prepare(
            "SELECT i.*, l.titre AS logement_titre, l.ville AS logement_ville
             FROM indisponibilites i
             JOIN logements l ON l.id = i.logement_id
             $where_sql
             ORDER BY i.date_debut DESC, i.id DESC
             LIMIT $per_page OFFSET $offset"
        );
        $stmt->execute($params);
        $items = $stmt->fetchAll();
    } catch (Exception $e) {
        $error = "Erreur lecture indisponibilités : " . $e->getMessage();
    }
}

$total_pages = max(1, (int)ceil($total / $per_page));

function build_query(array $extra = []): string {
    $merged = array_merge($_GET, $extra);
    return http_build_query($merged);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Indisponibilités (Calendrier)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1c1c1c, #6f42c1); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 20px; background-color: rgba(0,0,0,0.70); color: #fff; box-shadow: 0 8px 25px rgba(0,0,0,0.45); }
        .table thead th { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.15); }
        .table td { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.10); }
        .muted { color: rgba(255,255,255,0.75); }
        .form-control, .form-select { background-color: rgba(255,255,255,0.95); }
        .pagination .page-link { background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.15); }
        .pagination .page-item.active .page-link { background: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.25); }
    </style>
</head>
<body>
<div class="container py-4">

    <?php if (!$has_table): ?>
        <div class="alert alert-warning">
            <b>Table indisponibilites introuvable.</b>
            Exécute d'abord <code>migration_blocage.sql</code> (dans ce projet) pour activer le calendrier de blocage.
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['err'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="m-0">Indisponibilités (blocage par période)</h3>
            <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#addBlock">+ Ajouter un blocage</button>
        </div>

        <div class="collapse mt-3" id="addBlock">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label muted">Logement</label>
                    <select class="form-select" id="add_logement_id" name="logement_id" form="blockForm" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($logements as $l): ?>
                            <option value="<?= (int)$l['id'] ?>">
                                #<?= (int)$l['id'] ?> - <?= htmlspecialchars($l['ville'] ?? '') ?> - <?= htmlspecialchars($l['titre'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label muted">Date début</label>
                    <input class="form-control" type="date" name="date_debut" form="blockForm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label muted">Date fin</label>
                    <input class="form-control" type="date" name="date_fin" form="blockForm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label muted">Motif</label>
                    <input class="form-control" type="text" name="motif" form="blockForm" placeholder="Maintenance...">
                </div>
                <div class="col-12">
                    <form id="blockForm" method="POST" action="admin_blocage_action.php">
                        <input type="hidden" name="action" value="add">
                        <button class="btn btn-success">Enregistrer le blocage</button>
                        <span class="muted ms-2" style="font-size:0.9rem">Le logement ne pourra pas être réservé sur cette période (User + Admin).</span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label muted">Filtrer par logement</label>
                <select class="form-select" name="logement_id">
                    <option value="0">Tous</option>
                    <?php foreach ($logements as $l): ?>
                        <option value="<?= (int)$l['id'] ?>" <?= $logement_id === (int)$l['id'] ? 'selected' : '' ?>>
                            #<?= (int)$l['id'] ?> - <?= htmlspecialchars($l['ville'] ?? '') ?> - <?= htmlspecialchars($l['titre'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label muted">Du</label>
                <input class="form-control" type="date" name="from" value="<?= htmlspecialchars($from) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label muted">Au</label>
                <input class="form-control" type="date" name="to" value="<?= htmlspecialchars($to) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label muted">Recherche (instantanée)</label>
                <input class="form-control" id="liveSearch" type="text" placeholder="ville, titre, motif..." value="<?= htmlspecialchars($q) ?>">
                <input type="hidden" name="q" id="qHidden" value="<?= htmlspecialchars($q) ?>">
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-outline-light">OK</button>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-dark table-hover align-middle" id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Logement</th>
                        <th>Période</th>
                        <th>Motif</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$has_table): ?>
                    <tr><td colspan="5">Active la table via <code>migration_blocage.sql</code>.</td></tr>
                <?php elseif (empty($items)): ?>
                    <tr><td colspan="5">Aucun blocage.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?= (int)$it['id'] ?></td>
                            <td>
                                <div><?= htmlspecialchars($it['logement_titre'] ?? '') ?></div>
                                <div class="muted" style="font-size:0.85rem"><?= htmlspecialchars($it['logement_ville'] ?? '') ?> — logement #<?= (int)$it['logement_id'] ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($it['date_debut'] ?? '') ?></div>
                                <div><?= htmlspecialchars($it['date_fin'] ?? '') ?></div>
                            </td>
                            <td><?= htmlspecialchars($it['motif'] ?? '') ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-danger" href="admin_blocage_action.php?action=delete&id=<?= (int)$it['id'] ?>" onclick="return confirm('Supprimer ce blocage ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($has_table && $total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php $prev = max(1, $page - 1); $next = min($total_pages, $page + 1); ?>
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?<?= build_query(['page' => $prev]) ?>">«</a></li>
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    for ($p = $start; $p <= $end; $p++):
                    ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="?<?= build_query(['page' => $p]) ?>"><?= $p ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>"><a class="page-link" href="?<?= build_query(['page' => $next]) ?>">»</a></li>
                </ul>
            </nav>
        <?php endif; ?>

        
    </div>
</div>

<script>
// Live table search (instant)
(function(){
  const input = document.getElementById('liveSearch');
  const table = document.getElementById('dataTable');
  const qHidden = document.getElementById('qHidden');
  if(!input || !table) return;

  const rows = Array.from(table.querySelectorAll('tbody tr'));
  const filter = () => {
    const v = (input.value || '').toLowerCase();
    qHidden.value = input.value;
    rows.forEach(r => {
      const txt = r.innerText.toLowerCase();
      r.style.display = txt.includes(v) ? '' : 'none';
    });
  };
  input.addEventListener('input', filter);
  // init
  filter();
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
