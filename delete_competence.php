<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !isset($_GET['uid'])) {
    header('Location: login.php');
    exit;
}

$competenceId = (int) $_GET['id'];
$userId = (int) $_GET['uid'];
$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] == $userId;

if (!$isAdmin && !$isOwner) {
    header('Location: dashboard.php');
    exit;
}

// Vérifie si la compétence appartient bien à l'utilisateur
$check = $pdo->prepare("SELECT * FROM user_skills WHERE id = ? AND user_id = ?");
$check->execute([$competenceId, $userId]);

if (!$check->fetch()) {
    $_SESSION['toast'] = ['message' => '❌ Compétence introuvable ou accès refusé.', 'type' => 'danger'];
    header("Location: user_profile.php?id=$userId");
    exit;
}

// Suppression
$delete = $pdo->prepare("DELETE FROM user_skills WHERE id = ? AND user_id = ?");
$delete->execute([$competenceId, $userId]);

$_SESSION['toast'] = ['message' => '🗑️ Compétence supprimée.', 'type' => 'success'];
header("Location: user_profile.php?id=$userId");
exit;
?>
