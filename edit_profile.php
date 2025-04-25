<?php
require 'includes/config.php';
$pageTitle = "Modifier mon profil";
session_start();

$id = $_GET['id'] ?? null;
if (!$id || !isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $id)) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: dashboard.php');
    exit;
}

$avatarPath = $user['avatar'] ? 'uploads/avatars/' . $user['avatar'] : 'img/default_avatar.png';

$detailsStmt = $pdo->prepare("SELECT * FROM user_details WHERE user_id = ?");
$detailsStmt->execute([$id]);
$details = $detailsStmt->fetch() ?: [];

ob_start();
?>

<div class="container py-4">
    <h2 class="text-primary mb-4"><i class="fa fa-pen me-2"></i>Modifier le profil de <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h2>

    <ul class="nav nav-tabs mb-4" id="editTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="edit-info-tab" data-bs-toggle="tab" data-bs-target="#edit-info" type="button">🧾 Infos personnelles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="edit-identity-tab" data-bs-toggle="tab" data-bs-target="#edit-identity" type="button">🪪 Documents d'identité</button>
        </li>
    </ul>

    <div class="tab-content" id="editTabsContent">
        <div class="tab-pane fade show active" id="edit-info" role="tabpanel">
            <?php include 'partials/edit_form_infos.php'; ?>
        </div>
        <div class="tab-pane fade" id="edit-identity" role="tabpanel">
            <?php include 'partials/edit_form_identity.php'; ?>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= $_SESSION['role'] === 'admin' ? 'manage_users.php' : 'dashboard.php' ?>" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
