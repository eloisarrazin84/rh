<?php
require 'includes/config.php';
session_start();

if (!isset($_POST['user_id']) || !isset($_SESSION['user_id'])) {
    exit('Accès interdit');
}

$userId = (int) $_POST['user_id'];
$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] === $userId;

if (!$isAdmin && !$isOwner) {
    exit('Accès refusé');
}

// Créer un ZIP temporaire
$zip = new ZipArchive();
$tmpZipPath = tempnam(sys_get_temp_dir(), 'docs_');
$zip->open($tmpZipPath, ZipArchive::CREATE);

// Ajouter les documents
$stmt = $pdo->prepare("SELECT filename FROM documents WHERE user_id = ?");
$stmt->execute([$userId]);
$files = $stmt->fetchAll();

foreach ($files as $file) {
    $filePath = 'uploads/docs/' . $file['filename'];
    if (file_exists($filePath)) {
        $zip->addFile($filePath, $file['filename']);
    }
}

$zip->close();

// Envoyer le fichier au navigateur
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="documents_user_' . $userId . '.zip"');
header('Content-Length: ' . filesize($tmpZipPath));
readfile($tmpZipPath);
unlink($tmpZipPath); // Supprime le fichier temporaire après téléchargement
exit;
