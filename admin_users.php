<?php
require 'db.php';
require 'auth.php';
require_admin();

include 'admin_menu.php';

// Pagination
$per_page = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

$total = (int)($pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'] ?? 0);
$pages = max(1, (int)ceil($total / $per_page));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $per_page; }

$stmt = $pdo->prepare("SELECT id, nom, email, role, created_at FROM users ORDER BY created_at DESC LIMIT :lim OFFSET :off");
$stmt->bindValue(':lim', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #111, #6f42c1);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 20px;
            background-color: rgba(0,0,0,0.65);
            color: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }
        .table { color: #fff; }
        .table thead th { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.15); }
        .table td { color: rgba(255,255,255,0.85); border-bottom: 1px solid rgba(255,255,255,0.10); }
        .table tbody tr:hover { background-color: rgba(255,255,255,0.06); }
        .muted { color: rgba(255,255,255,0.75); }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="card p-3">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <div>
                <h3 class="m-0">Utilisateurs</h3>
                <div class="muted" style="font-size:0.9rem">Pagination + recherche instantanée</div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input id="liveSearch" type="text" class="form-control form-control-sm" placeholder="Rechercher (nom / email / rôle)…" style="max-width:320px">
            </div>
        </div>

        <div class="table-responsive">
            <table id="dataTable" class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Créé le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6">Aucun utilisateur.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= (int)$u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nom'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['role'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
                            <td class="text-end">
                                <?php if ((int)$u['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                    <?php if (($u['role'] ?? 'USER') === 'USER'): ?>
                                        <a class="btn btn-sm btn-success" href="admin_user_role.php?id=<?= (int)$u['id'] ?>&role=ADMIN&redirect=admin_users.php?page=<?= (int)$page ?>">Passer ADMIN</a>
                                    <?php else: ?>
                                        <a class="btn btn-sm btn-warning" href="admin_user_role.php?id=<?= (int)$u['id'] ?>&role=USER&redirect=admin_users.php?page=<?= (int)$page ?>">Passer USER</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <nav aria-label="Pagination" class="mt-2">
            <ul class="pagination pagination-sm justify-content-end m-0">
                <?php
                $prev = max(1, $page - 1);
                $next = min($pages, $page + 1);
                ?>
                <li class="page-item <?= $page<=1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin_users.php?page=<?= (int)$prev ?>">Précédent</a>
                </li>
                <li class="page-item disabled"><a class="page-link" href="#">Page <?= (int)$page ?> / <?= (int)$pages ?></a></li>
                <li class="page-item <?= $page>=$pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin_users.php?page=<?= (int)$next ?>">Suivant</a>
                </li>
            </ul>
        </nav>

    </div>
</div>

<script>
(function(){
    const input = document.getElementById('liveSearch');
    const table = document.getElementById('dataTable');
    if (!input || !table) return;

    function normalize(s){ return (s||'').toString().toLowerCase(); }

    input.addEventListener('input', function(){
        const q = normalize(input.value.trim());
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const txt = normalize(row.innerText);
            row.style.display = txt.includes(q) ? '' : 'none';
        });
    });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
