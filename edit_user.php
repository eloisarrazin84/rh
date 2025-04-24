<?php
require 'includes/config.php';
$pageTitle = "Modifier un utilisateur";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: manage_users.php');
    exit;
}

// Récupération de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $role      = $_POST['role'] ?? 'user';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($firstname && $lastname && $email) {
        $update = $pdo->prepare("UPDATE users SET firstname=?, lastname=?, email=?, role=?, is_active=? WHERE id=?");
        $update->execute([$firstname, $lastname, $email, $role, $is_active, $id]);
        $message = "<div class='alert alert-success'>✅ Utilisateur mis à jour avec succès.</div>";

        // Recharger les données
        $stmt->execute([$id]);
        $user = $stmt->fetch();
    } else {
        $message = "<div class='alert alert-warning'>⚠️ Tous les champs sont requis.</div>";
    }
}

ob_start();
?>

<h2 class="text-primary">Modifier l'utilisateur</h2>
<?= $message ?>

<form method="POST">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user['firstname']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user['lastname']) ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Rôle</label>
        <select name="role" class="form-select">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
        </select>
    </div>

    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="is_active" id="activeCheck" value="1" <?= $user['is_active'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="activeCheck">Compte actif</label>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="manage_users.php" class="btn btn-secondary">Annuler</a>
</form>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
