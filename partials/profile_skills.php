<?php
// Liste des compétences globales
$skills = $pdo->query("SELECT * FROM skills ORDER BY name ASC")->fetchAll();

// Compétences de l'utilisateur
$stmtSkills = $pdo->prepare("
    SELECT us.id, us.skill_id, us.valid_until, s.name
    FROM user_skills us
    JOIN skills s ON us.skill_id = s.id
    WHERE us.user_id = ?
    ORDER BY s.name
");
$stmtSkills->execute([$user['id']]);
$userSkills = $stmtSkills->fetchAll();
?>

<h5 class="mb-3 text-primary"><i class="fa fa-brain me-2"></i>Compétences</h5>

<?php if ($canEdit): ?>
<form action="assign_skill.php" method="POST" class="row gy-2 gx-3 align-items-end mb-4">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <div class="col-md-6">
        <label class="form-label">Compétence</label>
        <select name="skill_id" class="form-select" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($skills as $skill): ?>
                <option value="<?= $skill['id'] ?>"><?= htmlspecialchars($skill['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Valide jusqu'au</label>
        <input type="date" name="valid_until" class="form-control">
    </div>
    <div class="col-md-auto">
        <button type="submit" class="btn btn-outline-success">➕ Ajouter</button>
    </div>
</form>
<?php endif; ?>

<?php if ($userSkills): ?>
<table class="table table-bordered table-sm align-middle">
    <thead class="table-light">
        <tr>
            <th>Compétence</th>
            <th>Valide jusqu’au</th>
            <?php if ($canEdit): ?><th class="text-center">Actions</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($userSkills as $entry): 
            $expired = $entry['valid_until'] && (new DateTime($entry['valid_until'])) < new DateTime();
        ?>
        <tr>
            <td><?= htmlspecialchars($entry['name']) ?></td>
            <td>
                <?= $entry['valid_until'] ? date('d/m/Y', strtotime($entry['valid_until'])) : 'Non renseignée' ?>
                <?php if ($expired): ?>
                    <span class="badge bg-danger ms-2">Expirée</span>
                <?php endif; ?>
            </td>
            <?php if ($canEdit): ?>
            <td class="text-center">
                <a href="edit_user_skill.php?id=<?= $entry['id'] ?>&uid=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="delete_user_skill.php?id=<?= $entry['id'] ?>&uid=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette compétence ?');">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="text-muted">Aucune compétence enregistrée.</p>
<?php endif; ?>
