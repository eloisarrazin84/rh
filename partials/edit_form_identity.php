<form action="upload_identity_doc.php" method="POST" enctype="multipart/form-data" class="mb-4">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Type de document</label>
            <select name="doc_type" class="form-select" required>
                <option value="CNI">CNI</option>
                <option value="Permis">Permis</option>
                <option value="Passeport">Passeport</option>
                <option value="Carte professionnelle">Carte pro</option>
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
            <button type="submit" class="btn btn-outline-success">📤 Ajouter</button>
        </div>
    </div>
</form>
<?php
$docsQuery = $pdo->prepare("SELECT * FROM identity_documents WHERE user_id = ? ORDER BY uploaded_at DESC");
$docsQuery->execute([$user['id']]);
$identityDocs = $docsQuery->fetchAll();
?>

<?php if ($identityDocs): ?>
<ul class="list-group">
    <?php foreach ($identityDocs as $doc): 
        $isExpired = $doc['valid_until'] && (new DateTime($doc['valid_until'])) < new DateTime();
    ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <i class="fa fa-file me-2 text-muted"></i>
            <a href="uploads/docs/<?= htmlspecialchars($doc['filename']) ?>" target="_blank">
                <?= htmlspecialchars($doc['doc_type']) ?>
            </a>
            <span class="text-muted small ms-2">Ajouté le <?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?></span>
            <?php if ($doc['valid_until']): ?>
                <span class="ms-2 badge bg-<?= $isExpired ? 'danger' : 'secondary' ?>">
                    <?= $isExpired ? 'Expiré' : 'Valide jusqu’au ' . date('d/m/Y', strtotime($doc['valid_until'])) ?>
                </span>
            <?php endif; ?>
        </div>
        <a href="delete_identity_doc.php?id=<?= $doc['id'] ?>&uid=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce document ?');">
            <i class="fa fa-trash"></i>
        </a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
