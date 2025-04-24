<?php
require 'includes/config.php';
$pageTitle = "Modifier mon mot de passe";

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($newPassword) < 6) {
        $message = "<div class='alert alert-warning'>⚠️ Le nouveau mot de passe doit contenir au moins 6 caractères.</div>";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "<div class='alert alert-danger'>❌ Les nouveaux mots de passe ne correspondent pas.</div>";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && password_verify($oldPassword, $user['password'])) {
            $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$newHashed, $userId]);

            $_SESSION['toast'] = ['message' => 'Mot de passe modifié avec succès.', 'type' => 'success'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>❌ Ancien mot de passe incorrect.</div>";
        }
    }
}

ob_start();
?>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow-sm" style="max-width: 500px; width: 100%;">
        <h3 class="text-center text-primary mb-4">🔒 Modifier mon mot de passe</h3>

        <?= $message ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Ancien mot de passe</label>
                <input type="password" name="old_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Valider</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="text-muted">← Retour au dashboard</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
