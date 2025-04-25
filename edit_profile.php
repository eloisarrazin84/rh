<?php
require 'includes/config.php';
$pageTitle = "Modifier le profil";
session_start();

$id = $_GET['id'] ?? null;
if (!$id || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$canEdit = ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $id);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

$stmtDetail = $pdo->prepare("SELECT * FROM user_details WHERE user_id = ?");
$stmtDetail->execute([$id]);
$details = $stmtDetail->fetch();

ob_start();
?>

<div class="container py-4">
    <h2 class="mb-4 text-primary"><i class="fa fa-pen me-2"></i>Modifier le profil de <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h2>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="editTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="infos-tab" data-bs-toggle="tab" data-bs-target="#infos" type="button" role="tab">📋 Infos personnelles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="identity-tab" data-bs-toggle="tab" data-bs-target="#identity" type="button" role="tab">🪪 Documents d’identité</button>
        </li>
    </ul>

    <!-- Unified Form -->
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

        <div class="tab-content" id="editTabContent">
            <div class="tab-pane fade show active" id="infos" role="tabpanel">
                <?php include 'partials/edit_form_infos.php'; ?>
            </div>
            <div class="tab-pane fade" id="identity" role="tabpanel">
                <?php include 'partials/edit_form_identity.php'; ?>
            </div>
        </div>

        <!-- Save button -->
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-2"></i>Enregistrer les modifications
            </button>
        </div>
    </form>

    <div class="mt-3">
        <a href="<?= $_SESSION['role'] === 'admin' ? 'manage_users.php' : 'dashboard.php' ?>" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
