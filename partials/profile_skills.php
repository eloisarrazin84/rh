<?php
if (!isset($id)) {
    echo "<p class='text-danger'>Utilisateur introuvable.</p>";
    return;
}

$stmt = $pdo->prepare("
    SELECT us.id AS user_skill_id, s.name AS skill_name, us.valid_until
    FROM user_skills us
    JOIN skills s ON us.skill_id = s.id
    WHERE us.user_id = ?
    ORDER BY s.name ASC
");
$stmt->execute([$id]);
$userSkills = $stmt->fetchAll();
?>

<h5 class="mb-3 text-primary"><i class="fa fa-brain me-2"></i>Compétences</h5>

<?php if ($canEdit): ?>
<form action="assign_skill.php" method="POST" class="row g-3 align-items-end mb-4">
    <input type="hidden" name="user_id" value="<?= (int)$id ?>">
    <div class="col-md-6">
        <label class="form-label">Compétence</label>
        <select name="skill_id" class="form-select" required>
            <option value="">-- Choisir une compétence --</option>
            <?php
            $skills = $pdo->query("SELECT id, name FROM skills ORDER BY name ASC")->fetchAll();
            foreach ($skills as $skill) {
                echo "<option value=\"{$skill['id']}\">" . htmlspecialchars($skill['name']) . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Valide jusqu’au</label>
        <input type="date" name="valid_until" class="form-control">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-success w-100">✅ Ajouter</button>
    </div>
</form>
<?php endif; ?>

<?php if ($userSkills): ?>
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Compétence</th>
            <th>Valide jusqu’au</th>
            <?php if ($canEdit): ?><th>Actions</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($userSkills as $row): 
            $expired = $row['valid_until'] && new DateTime($row['valid_until']) < new DateTime();
        ?>
            <tr>
                <td><?= htmlspecialchars($row['skill_name']) ?></td>
                <td>
                    <?= $row['valid_until'] ? date('d/m/Y', strtotime($row['valid_until'])) : '<span class="text-muted">Non définie</span>' ?>
                    <?php if ($expired): ?>
                        <span class="badge bg-danger ms-2">Expirée</span>
                    <?php endif; ?>
                </td>
                <?php if ($canEdit): ?>
                <td>
                    <a href="edit_user_skill.php?id=<?= $row['user_skill_id'] ?>&uid=<?= $id ?>" class="btn btn-sm btn-outline-primary me-1">✏️</a>
                    <a href="delete_user_skill.php?id=<?= $row['user_skill_id'] ?>&uid=<?= $id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette compétence ?');">🗑️</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="text-muted">Aucune compétence enregistrée.</p>
<?php endif; ?>
