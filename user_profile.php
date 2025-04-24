<?php
require 'includes/config.php';
$pageTitle = "Profil utilisateur";

session_start();

$id = $_GET['id'] ?? null;
if (!$id || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$canEdit = ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $id);

$stmt = $pdo->prepare("SELECT id, firstname, lastname, email, role, is_active, created_at, avatar FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

$avatarPath = $user['avatar'] ? 'uploads/avatars/' . $user['avatar'] : 'img/default_avatar.png';

function getBadgeColor($type) {
    return match ($type) {
        'Diplôme' => 'success',
        'Attestation' => 'info',
        'Carte professionnelle' => 'warning',
        'Certificat médical' => 'danger',
        default => 'secondary',
    };
}

$filterType = $_GET['type'] ?? '';

if ($filterType) {
    $docQuery = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? AND doc_type = ? ORDER BY uploaded_at DESC");
    $docQuery->execute([$user['id'], $filterType]);
} else {
    $docQuery = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");
    $docQuery->execute([$user['id']]);
}
$documents = $docQuery->fetchAll();

ob_start();
?>

<div class="container py-4">
    <h2 class="text-primary mb-4"><i class="fa fa-user-circle me-2"></i>Profil de <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h2>

    <div class="row g-4">
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

    <!-- Filtre -->
    <form method="GET" class="row g-2 align-items-end mb-3">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <div class="col-md-4">
            <label class="form-label">Filtrer par type</label>
            <select name="type" class="form-select">
                <option value="">— Tous les documents —</option>
                <?php
                $types = ['Attestation', 'Diplôme', 'Carte professionnelle', 'Certificat médical', 'Autre'];
                foreach ($types as $type) {
                    $selected = ($filterType === $type) ? 'selected' : '';
                    echo "<option value=\"$type\" $selected>$type</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-outline-primary">Filtrer</button>
        </div>
    </form>

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
            <div class="col-md-4">
                <label class="form-label">Fichier (PDF, JPG...)</label>
                <input type="file" name="doc" accept=".pdf,.jpg,.jpeg,.png,.webp" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date de validité (optionnelle)</label>
                <input type="date" name="valid_until" class="form-control">
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
        <?php
            $color = getBadgeColor($doc['doc_type']);
            $badgeValid = '';

            if (!empty($doc['valid_until'])) {
                $expirationDate = new DateTime($doc['valid_until']);
                $today = new DateTime();
                $interval = $today->diff($expirationDate)->days;

                if ($expirationDate < $today) {
                    $badgeValid = '<span class="badge bg-danger ms-2">Expiré</span>';
                } elseif ($interval <= 30) {
                    $badgeValid = '<span class="badge bg-warning text-dark ms-2">À renouveler</span>';
                }
            }
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-<?= $color ?> me-2"><?= htmlspecialchars($doc['doc_type']) ?></span>
                <a href="uploads/docs/<?= htmlspecialchars($doc['filename']) ?>" target="_blank">
                    <?= htmlspecialchars($doc['filename']) ?>
                </a>
                <?php if (!empty($doc['valid_until'])): ?>
                    <div class="text-muted small">
                        Valide jusqu’au : <?= date('d/m/Y', strtotime($doc['valid_until'])) ?>
                        <?= $badgeValid ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($canEdit): ?>
                <a href="delete_document.php?id=<?= $doc['id'] ?>&uid=<?= $user['id'] ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Supprimer ce document ?');">
                    <i class="fa fa-trash"></i>
                </a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
    <?php else: ?>
        <p class="text-muted">Aucun document n’a encore été ajouté.</p>
    <?php endif; ?>
<?php if ($canEdit && count($documents) > 0): ?>
    <form action="download_documents.php" method="POST" class="mt-4">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <button type="submit" class="btn btn-outline-primary">
            <i class="fa fa-download me-2"></i>Télécharger tous les documents (.zip)
        </button>
    </form>
<?php endif; ?>

    <div class="mt-4">
        <a href="<?= $_SESSION['role'] === 'admin' ? 'manage_users.php' : 'dashboard.php' ?>" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
