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

$stmt = $pdo->prepare("SELECT filename FROM identity_documents WHERE user_id = ?");
$stmt->execute([$userId]);
$files = $stmt->fetchAll();

if (!$files) {
    exit("Aucun document à exporter.");
}

$zip = new ZipArchive();
$tmpZipPath = tempnam(sys_get_temp_dir(), 'identity_');

if ($zip->open($tmpZipPath, ZipArchive::OVERWRITE) !== TRUE) {
    exit("Impossible de créer l’archive.");
}

foreach ($files as $file) {
    $filePath = 'uploads/docs/' . $file['filename'];
    if (file_exists($filePath)) {
        $zip->addFile($filePath, $file['filename']);
    }
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="documents_identite_user_' . $userId . '.zip"');
header('Content-Length: ' . filesize($tmpZipPath));
readfile($tmpZipPath);
unlink($tmpZipPath);
exit;
