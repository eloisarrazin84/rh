<?php
require 'includes/config.php';
$pageTitle = "Connexion";

session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
header("Location: user_profile.php?id=" . $user['id']);
exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}

ob_start();
?>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h2 class="text-primary">🔒 Connexion</h2>
            <p class="text-muted">Espace RH Outdoor Secours</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Adresse e-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </div>
<div class="mt-3 text-center">
    <a href="forgot_password.php" class="text-decoration-none text-muted">
        <i class="fa fa-question-circle"></i> Mot de passe oublié ?
    </a>
</div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
