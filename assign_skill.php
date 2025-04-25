<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = $_POST['user_id'] ?? null;
$skill_id = $_POST['skill_id'] ?? null;
$valid_until = $_POST['valid_until'] ?? null;

if (!$user_id || !$skill_id) {
    $_SESSION['toast'] = ['message' => '❌ Utilisateur ou compétence manquant.', 'type' => 'danger'];
    header("Location: user_profile.php?id=" . urlencode($user_id));
    exit;
}

// Vérifie si la compétence existe déjà pour cet utilisateur
$check = $pdo->prepare("SELECT COUNT(*) FROM user_skills WHERE user_id = ? AND skill_id = ?");
$check->execute([$user_id, $skill_id]);
$exists = $check->fetchColumn() > 0;

if ($exists) {
    $_SESSION['toast'] = ['message' => '⚠️ Compétence déjà attribuée.', 'type' => 'warning'];
} else {
    $stmt = $pdo->prepare("INSERT INTO user_skills (user_id, skill_id, valid_until) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $skill_id, $valid_until]);
    $_SESSION['toast'] = ['message' => '✅ Compétence attribuée avec succès.', 'type' => 'success'];
}

header("Location: user_profile.php?id=" . urlencode($user_id));
exit;
?>
