<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id']) || !isset($_POST['skill_id'])) {
    http_response_code(400);
    echo "Requête invalide.";
    exit;
}

$userId = (int) $_POST['user_id'];
$skillId = (int) $_POST['skill_id'];
$validUntil = !empty($_POST['valid_until']) ? $_POST['valid_until'] : null;

// Vérification si déjà attribuée
$check = $pdo->prepare("SELECT COUNT(*) FROM user_skills WHERE user_id = ? AND skill_id = ?");
$check->execute([$userId, $skillId]);
$exists = $check->fetchColumn();

if ($exists) {
    $_SESSION['toast'] = [
        'message' => '❌ Compétence déjà attribuée.',
        'type' => 'warning'
    ];
    header("Location: user_profile.php?id=" . $userId);
    exit;
}

// Ajout
$stmt = $pdo->prepare("INSERT INTO user_skills (user_id, skill_id, valid_until) VALUES (?, ?, ?)");
$stmt->execute([$userId, $skillId, $validUntil]);

$_SESSION['toast'] = [
    'message' => '✅ Compétence attribuée avec succès.',
    'type' => 'success'
];
header("Location: user_profile.php?id=" . $userId);
exit;
?>
