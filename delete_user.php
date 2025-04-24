<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: manage_users.php');
    exit;
}

// Supprimer l'utilisateur
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// ✅ On enregistre une variable de session temporaire
$_SESSION['toast'] = ['message' => 'Utilisateur supprimé.', 'type' => 'danger'];

header('Location: manage_users.php');
exit;
