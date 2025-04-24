<?php
require 'includes/config.php';
session_start();

ob_clean(); // Vide tout output déjà envoyé (important)

if (!isset($_POST['user_id']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Accès interdit');
}

$userId = (int) $_POST['user_id'];
$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] === $userId;

if (!$isAdmin && !$isOwner) {
    http_response_code(403);
    exit('Accès refusé');
}

$stmt = $pdo->prepare("SELECT filename FROM documents WHERE user_id = ?");
$stmt->execute([$userId]);
$files = $stmt->fetchAll();

if (!$files) {
    exit("Aucun document à exporter.");
}

$zip = new ZipArchive();
$tmpZipPath = tempnam(sys_get_temp_dir(), 'docs_');

if ($zip->open($tmpZipPath, ZipArchive::OVERWRITE) !== TRUE) {
    exit("Impossible de créer l’archive.");
}

foreach ($files as $file) {
    $path = 'uploads/docs/' . $file['filename'];
    if (file_exists($path)) {
        $zip->addFile($path, $file['filename']);
    }
}
$zip->close();

// Important : force le téléchargement
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="documents_user_' . $userId . '.zip"');
header('Content-Length: ' . filesize($tmpZipPath));
readfile($tmpZipPath);
unlink($tmpZipPath); // nettoie le fichier temporaire
exit;
