<?php
require 'includes/config.php';
session_start();

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Accès refusé";
    exit;
}

// Récupère les données du formulaire
$userId = $_POST['user_id'] ?? null;
$skillId = $_POST['skill_id'] ?? null;
$level = $_POST['level'] ?? '';
$acquiredAt = $_POST['acquired_at'] ?? null;

if (!$userId || !$skillId) {
    $_SESSION['toast'] = ['message' => '❌ Données incomplètes.', 'type' => 'danger'];
    header("Location: user_profile.php?id=$userId");
    exit;
}

try {
    // Vérifie si la compétence est déjà assignée
    $check = $pdo->prepare("SELECT COUNT(*) FROM user_skills WHERE user_id = ? AND skill_id = ?");
    $check->execute([$userId, $skillId]);
    $exists = $check->fetchColumn() > 0;

    if ($exists) {
        // Mise à jour
        $update = $pdo->prepare("UPDATE user_skills SET level = ?, acquired_at = ? WHERE user_id = ? AND skill_id = ?");
        $update->execute([$level, $acquiredAt, $userId, $skillId]);
        $_SESSION['toast'] = ['message' => '🛠️ Compétence mise à jour.', 'type' => 'info'];
    } else {
        // Insertion
        $insert = $pdo->prepare("INSERT INTO user_skills (user_id, skill_id, level, acquired_at) VALUES (?, ?, ?, ?)");
        $insert->execute([$userId, $skillId, $level, $acquiredAt]);
        $_SESSION['toast'] = ['message' => '✅ Compétence ajoutée.', 'type' => 'success'];
    }
} catch (Exception $e) {
    $_SESSION['toast'] = ['message' => '❌ Erreur : ' . $e->getMessage(), 'type' => 'danger'];
}

header("Location: user_profile.php?id=$userId");
exit;
?>
