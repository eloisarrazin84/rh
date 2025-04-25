<?php
require 'includes/config.php';
$pageTitle = "Gestion des compétences utilisateurs";
session_start();

$stmtUsers = $pdo->prepare("SELECT u.id, u.firstname, u.lastname, GROUP_CONCAT(s.name) AS skills
                            FROM users u
                            LEFT JOIN user_skills us ON u.id = us.user_id
                            LEFT JOIN skills s ON us.skill_id = s.id
                            GROUP BY u.id");
$stmtUsers->execute();
$users = $stmtUsers->fetchAll();

ob_start();
?>

<div class="container py-4">
    <h2 class="text-primary mb-4"><i class="fa fa-users me-2"></i>Gestion des compétences des utilisateurs</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Compétences</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></td>
                    <td><?= htmlspecialchars($user['skills'] ?: 'Aucune compétence attribuée') ?></td>
                    <td>
                        <a href="edit_user_skill.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                            Modifier compétences
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-3">
        <a href="add_skill.php" class="btn btn-outline-primary">Ajouter une compétence</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
