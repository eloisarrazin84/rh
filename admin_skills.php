<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pageTitle = "Gestion des compétences";

// Récupération des compétences
$stmt = $pdo->query("SELECT * FROM skills ORDER BY name ASC");
$skills = $stmt->fetchAll();

ob_start();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="fa fa-brain me-2"></i>Gestion des compétences</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
            <i class="fa fa-plus me-1"></i> Ajouter une compétence
        </button>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nom</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($skills as $skill): ?>
            <tr>
                <td><?= htmlspecialchars($skill['name']) ?></td>
                <td class="text-end">
                    <form action="delete_skill.php" method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?= $skill['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette compétence ?')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal ajout compétence -->
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="add_skill.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSkillModalLabel">Ajouter une compétence</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="skillName" class="form-label">Nom de la compétence</label>
            <input type="text" class="form-control" id="skillName" name="name" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Ajouter</button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
