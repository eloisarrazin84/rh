<?php
$docsQuery = $pdo->prepare("SELECT * FROM identity_documents WHERE user_id = ? ORDER BY uploaded_at DESC");
$docsQuery->execute([$user['id']]);
$identityDocs = $docsQuery->fetchAll();
?>

<h5 class="mb-3 text-primary"><i class="fa fa-id-card me-2"></i>Documents d’identité</h5>

<?php if ($canEdit): ?>
<form action="upload_identity_doc.php" method="POST" enctype="multipart/form-data" class="mb-4">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Type de document</label>
            <select name="doc_type" class="form-select" required>
                <option value="CNI">Carte d'identité</option>
                <option value="Permis">Permis de conduire</option>
                <option value="Passeport">Passeport</option>
                <option value="Carte professionnelle">Carte professionnelle</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Fichier</label>
            <input type="file" name="doc" accept=".pdf,.jpg,.jpeg,.png,.webp" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Valide jusqu’au</label>
            <input type="date" name="valid_until" class="form-control">
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-outline-success">
                <i class="fa fa-upload me-1"></i>Ajouter
            </button>
        </div>
    </div>
</form>
<?php endif; ?>

<?php if ($identityDocs): ?>
<ul class="list-group shadow-sm">
    <?php foreach ($identityDocs as $doc): 
        $isExpired = $doc['valid_until'] && (new DateTime($doc['valid_until'])) < new DateTime();
        $badgeColor = $isExpired ? 'danger' : 'secondary';
        $badgeText = $isExpired ? 'Expiré' : 'Valide jusqu’au ' . date('d/m/Y', strtotime($doc['valid_until']));
    ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <i class="fa fa-file-alt me-2 text-muted"></i>
            <a href="uploads/docs/<?= htmlspecialchars($doc['filename']) ?>" target="_blank">
                <?= htmlspecialchars($doc['doc_type']) ?>
            </a>
            <small class="text-muted ms-2">Ajouté le <?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?></small>
            <?php if ($doc['valid_until']): ?>
                <span class="badge bg-<?= $badgeColor ?> ms-2"><?= $badgeText ?></span>
            <?php endif; ?>
        </div>
        <?php if ($canEdit): ?>
        <a href="delete_identity_doc.php?id=<?= $doc['id'] ?>&uid=<?= $user['id'] ?>" 
           class="btn btn-sm btn-outline-danger" 
           onclick="return confirm('Supprimer ce document ?');">
           <i class="fa fa-trash"></i>
        </a>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Téléchargement groupé -->
<?php if ($canEdit): ?>
<form action="download_identity_docs.php" method="POST" class="mt-3">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <button type="submit" class="btn btn-outline-primary">
        <i class="fa fa-download me-2"></i>Télécharger tous les documents d’identité (.zip)
    </button>
</form>
<?php endif; ?>

<?php else: ?>
<p class="text-muted">Aucun document d’identité n’a encore été ajouté.</p>
<?php endif; ?>
