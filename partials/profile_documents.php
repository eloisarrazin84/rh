<?php
$filterType = $_GET['type'] ?? '';

$docQuery = $filterType
    ? $pdo->prepare("SELECT * FROM documents WHERE user_id = ? AND doc_type = ? ORDER BY uploaded_at DESC")
    : $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");

$filterType ? $docQuery->execute([$user['id'], $filterType]) : $docQuery->execute([$user['id']]);
$documents = $docQuery->fetchAll();

function getBadgeColor($type) {
    return match ($type) {
        'Diplôme' => 'success',
        'Attestation' => 'info',
        'Carte professionnelle' => 'warning',
        'Certificat médical' => 'danger',
        default => 'secondary',
    };
}
?>

<form method="GET" class="row g-2 align-items-end mb-3">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <div class="col-md-4">
        <label class="form-label">Filtrer par type</label>
        <select name="type" class="form-select">
            <option value="">— Tous les documents —</option>
            <?php foreach (['Attestation', 'Diplôme', 'Carte professionnelle', 'Certificat médical', 'Autre'] as $type): ?>
                <option value="<?= $type ?>" <?= $filterType === $type ? 'selected' : '' ?>><?= $type ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-auto">
        <button type="submit" class="btn btn-outline-primary">Filtrer</button>
    </div>
</form>

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

<?php if ($documents): ?>
<ul class="list-group">
    <?php foreach ($documents as $doc): 
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
            <a href="uploads/docs/<?= htmlspecialchars($doc['filename']) ?>" target="_blank"><?= htmlspecialchars($doc['filename']) ?></a>
            <?php if (!empty($doc['valid_until'])): ?>
                <div class="text-muted small">Valide jusqu’au : <?= date('d/m/Y', strtotime($doc['valid_until'])) ?><?= $badgeValid ?></div>
            <?php endif; ?>
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

<?php if ($canEdit && count($documents) > 0): ?>
<form action="download_documents.php" method="POST" class="mt-3">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <button type="submit" class="btn btn-outline-primary"><i class="fa fa-download me-2"></i>Télécharger tous les documents (.zip)</button>
</form>
<?php endif; ?>
