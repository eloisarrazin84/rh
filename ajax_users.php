<?php
if (!isset($pdo)) {
    require 'includes/config.php';
}

$search     = $_GET['search'] ?? '';
$role       = $_GET['role'] ?? '';
$is_active  = $_GET['is_active'] ?? '';
$page       = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage    = 6;
$offset     = ($page - 1) * $perPage;

$queryBase = " FROM users WHERE 1";
$params = [];

if ($search) {
    $queryBase .= " AND (firstname LIKE :search OR lastname LIKE :search OR email LIKE :search)";
    $params['search'] = "%$search%";
}
if ($role) {
    $queryBase .= " AND role = :role";
    $params['role'] = $role;
}
if ($is_active !== '') {
    $queryBase .= " AND is_active = :active";
    $params['active'] = $is_active;
}

$countQuery = "SELECT COUNT(*)" . $queryBase;
$stmtCount = $pdo->prepare($countQuery);
$stmtCount->execute($params);
$totalUsers = $stmtCount->fetchColumn();
$totalPages = ceil($totalUsers / $perPage);

$dataQuery = "SELECT *" . $queryBase . " ORDER BY lastname ASC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($dataQuery);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="row">
<?php foreach ($users as $user): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-1"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h5>
                <p class="mb-1"><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p class="mb-1">
                    <strong>Rôle :</strong>
                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                        <?= ucfirst($user['role']) ?>
                    </span>
                </p>
                <p class="mb-3">
                    <strong>Statut :</strong>
                    <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                        <?= $user['is_active'] ? 'Actif' : 'Inactif' ?>
                    </span>
                </p>
                <div class="d-flex justify-content-between flex-wrap gap-1">
                    <a href="user_profile.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-info text-white">
    <i class="fa fa-user"></i> Profil
</a>
                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit"></i> Modifier
                    </a>
                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-outline-danger btn-sm"
                       onclick="return confirm('Supprimer ?');">
                        <i class="fa fa-trash"></i> Supprimer
                    </a>
                    <a href="toggle_user_status.php?id=<?= $user['id'] ?>" class="btn btn-sm <?= $user['is_active'] ? 'btn-outline-secondary' : 'btn-outline-success' ?> toggle-status">
                        <i class="fa <?= $user['is_active'] ? 'fa-toggle-off' : 'fa-toggle-on' ?>"></i>
                        <?= $user['is_active'] ? 'Désactiver' : 'Activer' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination justify-content-center mt-4">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                <a class="page-link page-link-ajax" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- Scripts AJAX -->
<script>
function reloadUserList() {
    const form = document.querySelector('#user-filter-form');
    const data = new URLSearchParams(new FormData(form));

    fetch('ajax_users.php?' + data)
        .then(res => res.text())
        .then(html => {
            document.querySelector('#user-list').innerHTML = html;
        });
}

// Pagination AJAX
document.querySelectorAll('.page-link-ajax').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        fetch(this.href)
            .then(res => res.text())
            .then(html => {
                document.querySelector('#user-list').innerHTML = html;
            });
    });
});

// Activation/Désactivation AJAX + toast
document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        fetch(this.href)
            .then(res => res.text())
            .then(() => {
                // 🔔 Affichage du toast Bootstrap
                const toastEl = document.getElementById('statusToast');
                const toastMsg = document.getElementById('toastMessage');
                const isActivating = this.textContent.includes('Activer');

                toastMsg.textContent = isActivating ? '✅ Utilisateur activé.' : '❌ Utilisateur désactivé.';
                toastEl.classList.remove('text-bg-success', 'text-bg-danger');
                toastEl.classList.add(isActivating ? 'text-bg-success' : 'text-bg-danger');

                const toast = new bootstrap.Toast(toastEl);
                toast.show();

                // 🔁 Recharge dynamique de la liste
                reloadUserList();
            });
    });
});
</script>
