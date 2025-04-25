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
        <?php if ($canEdit): ?>
            <a href="delete_identity_doc.php?id=<?= $doc['id'] ?>&uid=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce fichier ?');">
                <i class="fa fa-trash"></i>
            </a>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p class="text-muted">Aucun document d’identité enregistré.</p>
<?php endif; ?>

<?php if ($canEdit && count($identityDocs) > 0): ?>
<form action="download_identity_docs.php" method="POST" class="mt-3">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <button type="submit" class="btn btn-outline-primary"><i class="fa fa-download me-2"></i>Télécharger tous les documents d’identité (.zip)</button>
</form>
<?php endif; ?>
