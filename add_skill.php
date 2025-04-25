<?php
require 'includes/config.php';
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO skills (name) VALUES (?)");
        $stmt->execute([$name]);
        header("Location: admin_skills.php");
        exit;
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 text-primary">Ajouter une compétence</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="admin_skills.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
