<?php
require 'includes/config.php';
$pageTitle = "Profil utilisateur";
session_start();

$id = $_GET['id'] ?? null;

if (!$id || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Détermine si l'utilisateur peut modifier ce profil
$canEdit = ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $id);

// Récupération des infos utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

$avatarPath = $user['avatar'] ? 'uploads/avatars/' . $user['avatar'] : 'img/default_avatar.png';

// Infos administratives
$detailsStmt = $pdo->prepare("SELECT * FROM user_details WHERE user_id = ?");
$detailsStmt->execute([$id]);
$details = $detailsStmt->fetch();

// Documents RH
$documentsStmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");
$documentsStmt->execute([$id]);
$documents = $documentsStmt->fetchAll();

function displayValue($val) {
    return $val ? htmlspecialchars($val) : '<span class="text-muted">Non renseigné</span>';
}

ob_start();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="fa fa-user-circle me-2"></i>Profil de <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h2>
        <?php if ($canEdit): ?>
            <div>
                <a href="edit_profile.php?id=<?= $user['id'] ?>" class="btn btn-outline-primary me-2">
                    <i class="fa fa-pen me-1"></i> Modifier les informations
                </a>
                <a href="change_password.php?id=<?= $user['id'] ?>" class="btn btn-outline-danger">
                    <i class="fa fa-key me-1"></i> Réinitialiser le mot de passe
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">🧾 Infos personnelles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button">📂 Documents RH</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="skills-tab" data-bs-toggle="tab" data-bs-target="#skills" type="button">🧠 Compétences</button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabsContent">
        <!-- Onglet Infos -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <img src="<?= $avatarPath ?>" class="rounded-circle border shadow" width="140" height="140" alt="Avatar">
                    <?php if ($canEdit): ?>
                        <form action="upload_avatar.php" method="POST" enctype="multipart/form-data" class="mt-3">
                            <input type="hidden" name="from_profile" value="1">
                            <input type="file" name="avatar" accept="image/*" class="form-control mb-2" required>
                            <button type="submit" class="btn btn-outline-primary btn-sm">📤 Mettre à jour</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="col-md-8">
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($user['lastname']) ?></li>
                        <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($user['firstname']) ?></li>
                        <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></li>
                        <li class="list-group-item"><strong>Rôle :</strong> <?= ucfirst($user['role']) ?></li>
                        <li class="list-group-item">
                            <strong>Statut :</strong>
                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                <?= $user['is_active'] ? 'Actif' : 'Inactif' ?>
                            </span>
                        </li>
                        <li class="list-group-item"><strong>Créé le :</strong> <?= date('d/m/Y à H:i', strtotime($user['created_at'])) ?></li>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr><th>Civilité</th><td><?= displayValue($details['civility'] ?? null) ?></td></tr>
                                <tr><th>Adresse</th><td><?= displayValue($details['address'] ?? null) ?></td></tr>
                                <tr><th>Métier</th><td><?= displayValue($details['job'] ?? null) ?></td></tr>
                                <tr><th>Date de naissance</th><td><?= displayValue($details['birthdate'] ?? null) ?></td></tr>
                                <tr><th>Lieu de naissance</th><td><?= displayValue($details['birth_place'] ?? null) ?></td></tr>
                                <tr><th>Nationalité</th><td><?= displayValue($details['nationality'] ?? null) ?></td></tr>
                                <tr><th>Spécialité</th><td><?= displayValue($details['specialty'] ?? null) ?></td></tr>
                                <tr><th>RPPS</th><td><?= displayValue($details['rpps'] ?? null) ?></td></tr>
                                <tr><th>N° Sécurité Sociale</th><td><?= displayValue($details['social_security_number'] ?? null) ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Documents -->
        <div class="tab-pane fade" id="docs" role="tabpanel">
            <?php include 'partials/profile_documents.php'; ?>
        </div>

        <!-- Onglet Compétences -->
        <div class="tab-pane fade" id="skills" role="tabpanel">
            <?php include 'partials/profile_skills.php'; ?>
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
