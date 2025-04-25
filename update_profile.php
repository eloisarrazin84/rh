<?php
require 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_POST['user_id'];
$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] == $userId;

if (!$isAdmin && !$isOwner) {
    header("Location: dashboard.php");
    exit;
}

// Champs attendus
$fields = [
    'civility', 'address', 'birthdate', 'birth_place', 'nationality',
    'rpps', 'social_security_number', 'preferred_language'
];

$data = [];
foreach ($fields as $field) {
    $data[$field] = $_POST[$field] ?? null;
}

// Vérifie si des données existent déjà
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user_details WHERE user_id = ?");
$checkStmt->execute([$userId]);
$exists = $checkStmt->fetchColumn() > 0;

if ($exists) {
    // UPDATE
    $sql = "UPDATE user_details SET " . implode(', ', array_map(fn($f) => "$f = ?", $fields)) . " WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([...array_values($data), $userId]);
} else {
    // INSERT
    $sql = "INSERT INTO user_details (user_id, " . implode(', ', $fields) . ") VALUES (?, " . str_repeat('?, ', count($fields) - 1) . "?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, ...array_values($data)]);
}

// Message de confirmation
$_SESSION['toast'] = [
    'message' => '✅ Informations mises à jour.',
    'type' => 'success'
];

header("Location: user_profile.php?id=$userId");
exit;
?>
