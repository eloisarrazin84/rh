<?php
$docsQuery = $pdo->prepare("SELECT * FROM identity_documents WHERE user_id = ? ORDER BY uploaded_at DESC");
$docsQuery->execute([$user['id']]);
$identityDocs = $docsQuery->fetchAll();
?>

<h5 class="mb-3 text-primary"><i class="fa fa-id-card me-2"></i>Documents d’identité</h5>

<?php if ($canEdit): ?>
<div class="row g-3 mb-4 align-items-end">
    <div class="col-md-4">
        <label class="form-label">Type de document</label>
        <select name="identity_doc_type" class="form-select">
            <option value="CNI">Carte d'identité</option>
            <option value="Permis">Permis de conduire</option>
            <option value="Passeport">Passeport</option>
            <option value="Carte professionnelle">Carte professionnelle</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Fichier</label>
        <input type="file" name="identity_doc_file" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label">Valide jusqu’au</label>
        <input type="date" name="identity_doc_valid_until" class="form-control">
    </div>
</div>
<?php endif; ?>

<?php if ($identityDocs): ?>
<ul class="list-group">
    <?php foreach ($identityDocs as $doc): 
        $isExpired = $doc['valid_until'] && (new DateTime($doc['valid_until'])) < new DateTime();
    ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <i class="fa fa-file-alt me-2 text-muted"></i>
            <a href="uploads/docs/<?= htmlspecialchars($doc['filename']) ?>" target="_blank">
                <?= htmlspecialchars($doc['doc_type']) ?>
            </a>
            <small class="text-muted ms-2">Ajouté le <?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?></small>
            <?php if ($doc['valid_until']): ?>
                <span class="badge bg-<?= $isExpired ? 'danger' : 'secondary' ?> ms-2">
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
<?php else: ?>
<p class="text-muted">Aucun document d’identité enregistré.</p>
<?php endif; ?>
