<?php
$skillsStmt = $pdo->prepare("SELECT us.*, s.name FROM user_skills us JOIN skills s ON us.skill_id = s.id WHERE us.user_id = ? ORDER BY s.name");
$skillsStmt->execute([$user['id']]);
$userSkills = $skillsStmt->fetchAll();

if ($_SESSION['role'] === 'admin') {
    $allSkills = $pdo->query("SELECT * FROM skills ORDER BY name")->fetchAll();
}
?>
<div class="mb-3">
    <h5 class="text-primary"><i class="fa fa-brain me-2"></i>Compétences</h5>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <form method="POST" action="assign_skill.php" class="row g-2 mb-3">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <div class="col-md-5">
                <select name="skill_id" class="form-select" required>
                    <option value="">— Sélectionner une compétence —</option>
                    <?php foreach ($allSkills as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="level" class="form-select">
                    <option value="Base">Base</option>
                    <option value="Intermédiaire">Intermédiaire</option>
                    <option value="Avancé">Avancé</option>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-outline-success">➕ Ajouter</button>
            </div>
        </form>
    <?php endif; ?>

    <?php if ($userSkills): ?>
        <ul class="list-group">
            <?php foreach ($userSkills as $us): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($us['name']) ?> — <em class="text-muted"><?= $us['level'] ?></em></span>
                    <?php if ($canEdit): ?>
                        <a href="delete_user_skill.php?user_id=<?= $user['id'] ?>&skill_id=<?= $us['skill_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette compétence ?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">Aucune compétence affectée.</p>
    <?php endif; ?>
</div>
