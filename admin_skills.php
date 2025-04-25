<?php
require 'includes/config.php';
$pageTitle = "Gestion des compétences";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ajout de compétence
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $level = $_POST['level'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO skills (name, level) VALUES (?, ?)");
    $stmt->execute([$name, $level]);

    $_SESSION['toast'] = ['message' => '✅ Compétence ajoutée.', 'type' => 'success'];
    header("Location: admin_skills.php");
    exit;
}

// Suppression
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['toast'] = ['message' => '🗑️ Compétence supprimée.', 'type' => 'warning'];
    header("Location: admin_skills.php");
    exit;
}

$skills = $pdo->query("SELECT * FROM skills ORDER BY name")->fetchAll();

ob_start();
?>

<div class="container py-4">
    <h2 class="text-primary mb-4"><i class="fa fa-tools me-2"></i>Gestion des compétences</h2>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Nom de la compétence</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Niveau requis</label>
            <input type="text" name="level" class="form-control">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100"><i class="fa fa-plus"></i> Ajouter</button>
        </div>
    </form>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Niveau</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($skills as $skill): ?>
                <tr>
                    <td><?= htmlspecialchars($skill['name']) ?></td>
                    <td><?= htmlspecialchars($skill['level']) ?></td>
                    <td>
                        <a href="?delete=<?= $skill['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette compétence ?')">
                            <i class="fa fa-trash"></i>
                        </a>
                        <!-- Plus tard : bouton modifier -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
