<?php
require 'includes/config.php';
session_start();

$pageTitle = "Modifier le profil utilisateur";

$id = $_GET['id'] ?? null;
if (!$id || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$isAdmin = $_SESSION['role'] === 'admin';
$isOwner = $_SESSION['user_id'] == $id;

if (!$isAdmin && !$isOwner) {
    header('Location: dashboard.php');
    exit;
}

// Récupération des données utilisateurs
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$id]);
$user = $userStmt->fetch();

if (!$user) {
    header('Location: dashboard.php');
    exit;
}

// Détails administratifs
$detailStmt = $pdo->prepare("SELECT * FROM user_details WHERE user_id = ?");
$detailStmt->execute([$id]);
$details = $detailStmt->fetch();

if (!$details) {
    // Initialisation vide si aucun détail
    $details = array_fill_keys([
        'civility', 'address', 'job_title', 'birth_date', 'birth_city',
        'birth_department', 'nationality', 'specialty', 'rpps',
        'social_security_number', 'passport_number', 'passport_issued_at',
        'passport_expires_at', 'cni_number', 'cni_issued_at', 'cni_expires_at',
        'driving_license', 'newsletter_subscribed', 'preferred_language'
    ], '');
}

ob_start();
?>

<div class="container py-4">
    <a href="user_profile.php?id=<?= $id ?>" class="btn btn-outline-secondary mb-3">← Retour au profil</a>
    <h2 class="mb-4 text-primary"><i class="fa fa-user-edit me-2"></i>Édition du profil de <?= htmlspecialchars($user['firstname']) ?> <?= htmlspecialchars($user['lastname']) ?></h2>

    <form action="save_profile.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= $id ?>">

        <!-- Onglets -->
        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#infos">Informations</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#identite">Identité</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contact">Contact</a></li>
        </ul>

        <div class="tab-content">

            <!-- Onglet Informations -->
            <div class="tab-pane fade show active" id="infos">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Civilité</label>
                        <select name="civility" class="form-select">
                            <option value="">—</option>
                            <option value="Monsieur" <?= $details['civility'] === 'Monsieur' ? 'selected' : '' ?>>Monsieur</option>
                            <option value="Madame" <?= $details['civility'] === 'Madame' ? 'selected' : '' ?>>Madame</option>
                            <option value="Autre" <?= $details['civility'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($details['address']) ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Métier</label>
                        <input type="text" name="job_title" value="<?= htmlspecialchars($details['job_title']) ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Spécialité</label>
                        <input type="text" name="specialty" value="<?= htmlspecialchars($details['specialty']) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date de naissance</label>
                        <input type="date" name="birth_date" value="<?= $details['birth_date'] ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lieu de naissance</label>
                        <input type="text" name="birth_city" value="<?= htmlspecialchars($details['birth_city']) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Département</label>
                        <input type="text" name="birth_department" value="<?= htmlspecialchars($details['birth_department']) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nationalité</label>
                        <input type="text" name="nationality" value="<?= htmlspecialchars($details['nationality']) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">RPPS</label>
                        <input type="text" name="rpps" value="<?= htmlspecialchars($details['rpps']) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">N° Sécurité sociale</label>
                        <input type="text" name="social_security_number" value="<?= htmlspecialchars($details['social_security_number']) ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Langue préférée</label>
                        <input type="text" name="preferred_language" value="<?= htmlspecialchars($details['preferred_language']) ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-check-label d-block">Abonnement aux notifications</label>
                        <input type="checkbox" name="newsletter_subscribed" value="1" class="form-check-input" <?= $details['newsletter_subscribed'] ? 'checked' : '' ?>> Oui
                    </div>
                </div>
            </div>

            <!-- Onglet Identité -->
            <div class="tab-pane fade" id="identite">
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Numéro de CNI</label>
                        <input type="text" name="cni_number" value="<?= htmlspecialchars($details['cni_number']) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Délivrée le</label>
                        <input type="date" name="cni_issued_at" value="<?= $details['cni_issued_at'] ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expire le</label>
                        <input type="date" name="cni_expires_at" value="<?= $details['cni_expires_at'] ?>" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Numéro de passeport</label>
                        <input type="text" name="passport_number" value="<?= htmlspecialchars($details['passport_number']) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Délivré le</label>
                        <input type="date" name="passport_issued_at" value="<?= $details['passport_issued_at'] ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expire le</label>
                        <input type="date" name="passport_expires_at" value="<?= $details['passport_expires_at'] ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-check-label d-block">Permis de conduire</label>
                        <input type="checkbox" name="driving_license" value="1" class="form-check-input" <?= $details['driving_license'] ? 'checked' : '' ?>> Oui
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">📤 Fichiers d’identité (PDF, JPG)</label>
                        <input type="file" name="identity_files[]" class="form-control" multiple>
                    </div>
                    <div class="col-md-6">
    <label class="form-label">Date de validité globale (facultatif)</label>
    <input type="date" name="identity_valid_until" class="form-control">
</div>

                </div>
            </div>

            <!-- Onglet Contact (à venir) -->
            <div class="tab-pane fade" id="contact">
                <p class="mt-3 text-muted">À venir : gestion des téléphones et e-mails secondaires</p>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">💾 Enregistrer les modifications</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
