<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Accès refusé";
    exit;
}

$skillId = $_GET['skill_id'] ?? null;
$userId = $_GET['user_id'] ?? null;

if (!$skillId || !$userId) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM user_skills WHERE user_id = ? AND skill_id = ?");
$stmt->execute([$userId, $skillId]);

$_SESSION['toast'] = [
    'message' => '✅ Compétence supprimée avec succès.',
    'type' => 'success'
];

header("Location: ../user_profile.php?id=$userId");
exit;
?>
