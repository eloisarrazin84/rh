<?php
require 'includes/config.php';
$pageTitle = "Mot de passe oublié";

session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // À compléter : génération de token et envoi email réel
        $message = "<div class='alert alert-info'>📧 Si cette adresse existe, un lien de réinitialisation vous a été envoyé.</div>";
    } else {
        $message = "<div class='alert alert-info'>📧 Si cette adresse existe, un lien de réinitialisation vous a été envoyé.</div>";
    }
}

ob_start();
?>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-primary text-center mb-4">🔑 Mot de passe oublié</h3>
        <?= $message ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Votre adresse e-mail</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Envoyer un lien</button>
            </div>
        </form>
        <div class="mt-3 text-center">
            <a href="login.php" class="text-muted">← Retour à la connexion</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
