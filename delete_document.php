<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'], $_GET['id'], $_GET['uid'])) {
    exit;
}

$docId = (int) $_GET['id'];
$uid = (int) $_GET['uid'];
$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] == $uid;

if (!$isAdmin && !$isOwner) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT filename FROM documents WHERE id = ? AND user_id = ?");
$stmt->execute([$docId, $uid]);
$doc = $stmt->fetch();

if ($doc) {
    @unlink("uploads/docs/" . $doc['filename']);
    $del = $pdo->prepare("DELETE FROM documents WHERE id = ?");
    $del->execute([$docId]);

    $_SESSION['toast'] = ['message' => '🗑️ Document supprimé.', 'type' => 'warning'];
}

header("Location: user_profile.php?id=" . $uid);
exit;
