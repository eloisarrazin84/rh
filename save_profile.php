<?php
require 'includes/config.php';
session_start();

if (!isset($_POST['user_id']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_POST['user_id'];
$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] === $userId;

if (!$isAdmin && !$isOwner) {
    header("Location: dashboard.php");
    exit;
}

// Préparer les champs
$data = [
    'civility' => $_POST['civility'] ?? null,
    'address' => $_POST['address'] ?? null,
    'job_title' => $_POST['job_title'] ?? null,
    'birth_date' => $_POST['birth_date'] ?: null,
    'birth_city' => $_POST['birth_city'] ?? null,
    'birth_department' => $_POST['birth_department'] ?? null,
    'nationality' => $_POST['nationality'] ?? null,
    'specialty' => $_POST['specialty'] ?? null,
    'rpps' => $_POST['rpps'] ?? null,
    'social_security_number' => $_POST['social_security_number'] ?? null,
    'preferred_language' => $_POST['preferred_language'] ?? null,
    'newsletter_subscribed' => isset($_POST['newsletter_subscribed']) ? 1 : 0,
    'cni_number' => $_POST['cni_number'] ?? null,
    'cni_issued_at' => $_POST['cni_issued_at'] ?: null,
    'cni_expires_at' => $_POST['cni_expires_at'] ?: null,
    'passport_number' => $_POST['passport_number'] ?? null,
    'passport_issued_at' => $_POST['passport_issued_at'] ?: null,
    'passport_expires_at' => $_POST['passport_expires_at'] ?: null,
    'driving_license' => isset($_POST['driving_license']) ? 1 : 0
];

// Vérifier si une ligne existe
$stmt = $pdo->prepare("SELECT id FROM user_details WHERE user_id = ?");
$stmt->execute([$userId]);

if ($stmt->fetch()) {
    // UPDATE
    $sql = "UPDATE user_details SET ";
    $sql .= implode(", ", array_map(fn($k) => "$k = :$k", array_keys($data)));
    $sql .= ", updated_at = NOW() WHERE user_id = :user_id";
    $data['user_id'] = $userId;
    $pdo->prepare($sql)->execute($data);
} else {
    // INSERT
    $fields = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));
    $sql = "INSERT INTO user_details (user_id, $fields) VALUES (:user_id, $placeholders)";
    $data['user_id'] = $userId;
    $pdo->prepare($sql)->execute($data);
}

// Upload des fichiers d'identité
if (!empty($_FILES['identity_files']['name'][0])) {
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    foreach ($_FILES['identity_files']['tmp_name'] as $i => $tmpName) {
        if ($_FILES['identity_files']['error'][$i] === UPLOAD_ERR_OK &&
            in_array($_FILES['identity_files']['type'][$i], $allowedTypes)) {
            
            $ext = pathinfo($_FILES['identity_files']['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid("doc_") . "." . $ext;
            $path = "uploads/docs/" . $filename;

            move_uploaded_file($tmpName, $path);

            $docStmt = $pdo->prepare("INSERT INTO identity_documents (user_id, doc_type, filename) VALUES (?, ?, ?)");
            $docStmt->execute([$userId, 'Autre', $filename]);
$validUntil = $_POST['identity_valid_until'] ?? null;
$docStmt = $pdo->prepare("INSERT INTO identity_documents (user_id, doc_type, filename, valid_until) VALUES (?, ?, ?, ?)");
$docStmt->execute([$userId, 'Autre', $filename, $validUntil ?: null]);
        }
    }
}

$_SESSION['toast'] = ['message' => '✅ Profil mis à jour avec succès.', 'type' => 'success'];
header("Location: user_profile.php?id=$userId");
exit;
