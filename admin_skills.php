<?php
require 'includes/config.php';
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}
$pageTitle = "Gestion des compétences";
$skills = $pdo->query("SELECT * FROM skills ORDER BY name")->fetchAll();
?>
<div class="container py-4">
    <h2 class="text-primary mb-4"><i class="fa fa-cogs me-2"></i>Gestion des compétences</h2>
    <a href="add_skill.php" class="btn btn-success mb-3"><i class="fa fa-plus me-1"></i>Ajouter une compétence</a>
    <table class="table table-bordered">
        <thead><tr><th>Nom</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($skills as $skill): ?>
            <tr>
                <td><?= htmlspecialchars($skill['name']) ?></td>
                <td>
                    <a href="edit_skill.php?id=<?= $skill['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                    <a href="delete_skill.php?id=<?= $skill['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
