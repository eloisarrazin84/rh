<?php
require 'includes/config.php';
$pageTitle = "Photo de profil";

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];

    // Validation rapide
    if ($file['error'] === UPLOAD_ERR_OK && $file['size'] < 2 * 1024 * 1024) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($file['type'], $allowed)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = "user_$userId." . $ext;
            $path = "uploads/avatars/" . $filename;

            move_uploaded_file($file['tmp_name'], $path);

            // Enregistrer le nom du fichier en BDD
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$filename, $userId]);

            $_SESSION['toast'] = ['message' => 'Photo de profil mise à jour.', 'type' => 'success'];
            header("Location: user_profile.php?id=$userId");
            exit;
        } else {
            $message = "<div class='alert alert-warning'>❌ Format non autorisé. JPEG, PNG ou WebP uniquement.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>⚠️ Erreur ou fichier trop volumineux (max 2Mo).</div>";
    }
}

// Récupérer avatar actuel
$stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->execute([$userId]);
$avatar = $stmt->fetchColumn();
$avatarPath = $avatar ? "uploads/avatars/$avatar" : "img/default_avatar.png";

ob_start();
?>

<div class="container py-4">
    <h2 class="text-primary mb-4">🖼️ Modifier ma photo de profil</h2>

    <?= $message ?>

    <div class="text-center mb-4">
        <img src="<?= htmlspecialchars($avatarPath) ?>" class="rounded-circle border shadow" width="120" height="120" alt="Avatar">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="avatar" class="form-label">Choisir une nouvelle photo (JPEG, PNG, WebP - max 2Mo)</label>
            <input type="file" name="avatar" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
