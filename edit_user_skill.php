<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$skill_id = $_GET['skill_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$skill_id || !$user_id) {
    header("Location: user_profile.php?id=$user_id");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ? AND skill_id = ?");
$stmt->execute([$user_id, $skill_id]);
$userSkill = $stmt->fetch();

if (!$userSkill) {
    $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Compétence non trouvée.'];
    header("Location: user_profile.php?id=$user_id");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid_until = $_POST['valid_until'] ?? null;
    $stmt = $pdo->prepare("UPDATE user_skills SET valid_until = ? WHERE user_id = ? AND skill_id = ?");
    $stmt->execute([$valid_until, $user_id, $skill_id]);

    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Compétence mise à jour.'];
    header("Location: user_profile.php?id=$user_id");
    exit;
}

// Formulaire HTML
?>
<?php include 'includes/layout.php'; ?>

<div class="container mt-4">
    <h4 class="text-primary">Modifier la validité de la compétence</h4>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Date de validité</label>
            <input type="date" name="valid_until" class="form-control" value="<?= htmlspecialchars($userSkill['valid_until']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="user_profile.php?id=<?= $user_id ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
