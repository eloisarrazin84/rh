<?php
require 'includes/config.php';
session_start();
ob_start();

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

// Récupération du type de document (depuis le <select>)
$docType = $_POST['doc_type'] ?? 'Autre';

if ($_FILES['doc']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['doc'];
    $allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];

    if (in_array($file['type'], $allowed) && $file['size'] <= 3 * 1024 * 1024) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid("doc_") . "." . $ext;
        $path = "uploads/docs/" . $filename;

        // Vérifie si le dossier existe
        if (!is_dir('uploads/docs')) {
            mkdir('uploads/docs', 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $path)) {
            $stmt = $pdo->prepare("INSERT INTO documents (user_id, filename, doc_type) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $filename, $docType]);

            $_SESSION['toast'] = ['message' => '📁 Document ajouté avec succès.', 'type' => 'success'];
        } else {
            $_SESSION['toast'] = ['message' => '❌ Erreur lors du déplacement du fichier.', 'type' => 'danger'];
        }
    } else {
        $_SESSION['toast'] = ['message' => '❌ Type ou taille de fichier invalide (max 3 Mo).', 'type' => 'danger'];
    }
}

header("Location: user_profile.php?id=" . $userId);
exit;
