<?php
$skillsQuery = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ?");
$skillsQuery->execute([$user['id']]);
$skills = $skillsQuery->fetchAll();
?>

<div class="mb-3">
  <h5 class="text-primary"><i class="fa fa-certificate me-2"></i>Liste des compétences</h5>

  <?php if ($skills): ?>
    <ul class="list-group">
      <?php foreach ($skills as $skill): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($skill['name']) ?>
          <span class="badge bg-secondary"><?= htmlspecialchars($skill['level']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="text-muted">Aucune compétence enregistrée.</p>
  <?php endif; ?>
</div>

<?php if ($canEdit): ?>
<hr>
<form action="add_skill.php" method="POST" class="row g-2">
  <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
  <div class="col-md-6">
    <input type="text" name="name" class="form-control" placeholder="Compétence (ex : PSC1)" required>
  </div>
  <div class="col-md-4">
    <select name="level" class="form-select" required>
      <option value="">Niveau</option>
      <option value="Débutant">Débutant</option>
      <option value="Confirmé">Confirmé</option>
      <option value="Expert">Expert</option>
    </select>
  </div>
  <div class="col-md-2">
    <button class="btn btn-outline-success w-100"><i class="fa fa-plus"></i> Ajouter</button>
  </div>
</form>
<?php endif; ?>
