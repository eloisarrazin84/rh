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

// Champs obligatoires (sauf rpps)
$fields = [
    'civility' => true,
    'address' => true,
    'birthdate' => true,
    'birth_place' => true,
    'nationality' => true,
    'rpps' => false, // facultatif
    'social_security_number' => true
];

$data = [];
foreach ($fields as $field => $isRequired) {
    $value = trim($_POST[$field] ?? '');

    if ($isRequired && empty($value)) {
        $_SESSION['toast'] = [
            'message' => "❌ Le champ '$field' est requis.",
            'type' => 'danger'
        ];
        header("Location: edit_profile.php?id=$userId");
        exit;
    }

    $data[$field] = $value ?: null; // mettre à null si vide pour les champs facultatifs
}

// Vérifie si une ligne existe déjà
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user_details WHERE user_id = ?");
$checkStmt->execute([$userId]);
$exists = $checkStmt->fetchColumn() > 0;

if ($exists) {
    // UPDATE
    $sql = "UPDATE user_details SET " . implode(', ', array_map(fn($f) => "$f = ?", array_keys($data))) . " WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([...array_values($data), $userId]);
} else {
    // INSERT
    $sql = "INSERT INTO user_details (user_id, " . implode(', ', array_keys($data)) . ") VALUES (?, " . str_repeat('?, ', count($data) - 1) . "?)";
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
