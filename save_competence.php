<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_POST['user_id'];
$competenceId = isset($_POST['competence_id']) && $_POST['competence_id'] !== '' ? (int) $_POST['competence_id'] : null;
$name = trim($_POST['name'] ?? '');
$acquiredAt = $_POST['acquired_at'] ?? null;

$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] == $userId;

if (!$isAdmin && !$isOwner) {
    header('Location: dashboard.php');
    exit;
}

// Validation
if (empty($name) || empty($acquiredAt)) {
    $_SESSION['toast'] = ['message' => '❌ Tous les champs sont requis.', 'type' => 'danger'];
    header("Location: user_profile.php?id=$userId");
    exit;
}

if ($competenceId) {
    // Mise à jour
    $stmt = $pdo->prepare("UPDATE user_skills SET name = ?, acquired_at = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$name, $acquiredAt, $competenceId, $userId]);
    $msg = '✏️ Compétence mise à jour.';
} else {
    // Insertion
    $stmt = $pdo->prepare("INSERT INTO user_skills (user_id, name, acquired_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $name, $acquiredAt]);
    $msg = '✅ Compétence ajoutée.';
}

$_SESSION['toast'] = ['message' => $msg, 'type' => 'success'];
header("Location: user_profile.php?id=$userId");
exit;
?>
