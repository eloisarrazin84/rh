<?php
require 'includes/config.php';
session_start();

$id = $_GET['id'] ?? null;
$uid = $_GET['uid'] ?? null;

if (!$id || !$uid || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] == $uid;

if (!$isAdmin && !$isOwner) {
    header("Location: dashboard.php");
    exit;
}

// Récupération du fichier
$stmt = $pdo->prepare("SELECT filename FROM identity_documents WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $uid]);
$doc = $stmt->fetch();

if ($doc) {
    $filepath = 'uploads/docs/' . $doc['filename'];
    if (file_exists($filepath)) {
        unlink($filepath);
    }

    $pdo->prepare("DELETE FROM identity_documents WHERE id = ?")->execute([$id]);
    $_SESSION['toast'] = ['message' => '🗑️ Document supprimé.', 'type' => 'success'];
}

header("Location: user_profile.php?id=$uid");
exit;
