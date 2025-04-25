<?php
// Récupération des compétences depuis la base
$skillsStmt = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ? ORDER BY acquired_at DESC");
$skillsStmt->execute([$user['id']]);
$skills = $skillsStmt->fetchAll();
?>

<?php if ($canEdit): ?>
<form action="add_skill.php" method="POST" class="row g-3 align-items-end mb-4">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <div class="col-md-6">
        <label class="form-label">Compétence</label>
        <input type="text" name="skill_name" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date d'acquisition</label>
        <input type="date" name="acquired_at" class="form-control" required>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-success w-100">Ajouter</button>
    </div>
</form>
<?php endif; ?>

<?php if ($skills): ?>
<ul class="list-group">
    <?php foreach ($skills as $skill): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <?= htmlspecialchars($skill['skill_name']) ?>
                <small class="text-muted ms-2">(<?= date('d/m/Y', strtotime($skill['acquired_at'])) ?>)</small>
            </div>
            <?php if ($canEdit): ?>
            <form action="delete_skill.php" method="POST" onsubmit="return confirm('Supprimer cette compétence ?');">
                <input type="hidden" name="id" value="<?= $skill['id'] ?>">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
            </form>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p class="text-muted">Aucune compétence enregistrée.</p>
<?php endif; ?>
