<?php
require 'includes/config.php';
$pageTitle = "Créer un utilisateur";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? 'user';

    if ($firstname && $lastname && $email && $password && in_array($role, ['user', 'admin'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "<div class='alert alert-danger'>❌ Cet email est déjà utilisé.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role, is_active)
                                   VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$firstname, $lastname, $email, $hashed_password, $role]);

            // ✅ Toast
            $_SESSION['toast'] = ['message' => 'Utilisateur créé avec succès.', 'type' => 'success'];
            header("Location: manage_users.php");
            exit;
        }
    } else {
        $message = "<div class='alert alert-warning'>⚠️ Tous les champs sont requis.</div>";
    }
}

ob_start();
?>

<h2 class="text-primary">Créer un nouvel utilisateur</h2>
<?= $message ?>

<form method="POST">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="firstname" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="lastname" class="form-control" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Rôle</label>
        <select name="role" class="form-select" required>
            <option value="user">Utilisateur</option>
            <option value="admin">Administrateur</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Créer</button>
    <a href="manage_users.php" class="btn btn-secondary">Annuler</a>
</form>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
