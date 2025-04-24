<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    exit;
}

// Récupère l'état actuel
$stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit;
}

$newStatus = $user['is_active'] ? 0 : 1;

$update = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
$update->execute([$newStatus, $id]);

echo "ok";
