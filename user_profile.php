<?php
require 'includes/config.php';
$pageTitle = "Profil utilisateur";

session_start();

$id = $_GET['id'] ?? null;
if (!$id || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Admin ou l'utilisateur lui-même
$canEdit = ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $id);

// Récupérer les infos
$stmt = $pdo->prepare("SELECT id, firstname, lastname, email, role, is_active, created_at, avatar FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

$avatarPath = $user['avatar'] ? 'uploads/avatars/' . $user['avatar'] : 'img/default_avatar.png';

ob_start();
?>

<div class="container py-4">
    <h2 class="text-primary mb-4"><i class="fa fa-user-circle me-2"></i>Profil de <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h2>

    <div class="row g-4">

        <!-- Avatar + modification -->
        <div class="col-md-4 text-center">
            <img src="<?= htmlspecialchars($avatarPath) ?>" class="rounded-circle border shadow" width="140" height="140" alt="Avatar">

            <?php if ($canEdit): ?>
                <form action="upload_avatar.php" method="POST" enctype="multipart/form-data" class="mt-3">
                    <input type="hidden" name="from_profile" value="1">
                    <input type="file" name="avatar" accept="image/*" class="form-control mb-2" required>
                    <button type="submit" class="btn btn-outline-primary btn-sm">📤 Mettre à jour</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Infos utilisateur -->
        <div class="col-md-8">
            <ul class="list-group">
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
        </div>

    </div>
<hr class="my-5">
<h4 class="text-primary"><i class="fa fa-folder-open me-2"></i>Documents personnels</h4>

<?php
// Récupérer les documents
$docQuery = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");
$docQuery->execute([$user['id']]);
$documents = $docQuery->fetchAll();
?>

<!-- Upload -->
<?php if ($canEdit): ?>
<form action="upload_document.php" method="POST" enctype="multipart/form-data" class="mb-4">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Type de document</label>
            <select name="doc_type" class="form-select" required>
                <option value="Attestation">📜 Attestation</option>
                <option value="Diplôme">🎓 Diplôme</option>
                <option value="Carte professionnelle">🪪 Carte professionnelle</option>
                <option value="Certificat médical">🩺 Certificat médical</option>
                <option value="Autre" selected>📁 Autre</option>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label">Fichier (PDF, JPG...)</label>
            <input type="file" name="doc" accept=".pdf,.jpg,.jpeg,.png,.webp" class="form-control" required>
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-outline-success mt-2">📤 Envoyer</button>
        </div>
    </div>
</form>

<?php endif; ?>

<!-- Liste des documents -->
<?php if ($documents): ?>
<ul class="list-group">
    <?php foreach ($documents as $doc): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-primary me-2"><?= htmlspecialchars($doc['doc_type']) ?></span>
                <a href="uploads/docs/<?= htmlspecialchars($doc['filename']) ?>" target="_blank">
                    <?= htmlspecialchars($doc['filename']) ?>
                </a>
            </div>
            <?php if ($canEdit): ?>
                <a href="delete_document.php?id=<?= $doc['id'] ?>&uid=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce document ?');">
                    <i class="fa fa-trash"></i>
                </a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php else: ?>
<p class="text-muted">Aucun document n’a encore été ajouté.</p>
<?php endif; ?>

    <div class="mt-4">
        <a href="<?= $_SESSION['role'] === 'admin' ? 'manage_users.php' : 'dashboard.php' ?>" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
