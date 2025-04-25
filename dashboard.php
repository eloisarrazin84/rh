<?php
require 'includes/config.php';
$pageTitle = "Dashboard";

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'] ?? 'user';

ob_start();
?>

<style>
.timeline {
  border-left: 3px solid #0d6efd;
  margin-left: 1rem;
  padding-left: 1rem;
  position: relative;
}
.timeline-item {
  position: relative;
  margin-bottom: 1.5rem;
  padding-left: 0.5rem;
  animation: fadein 0.6s ease forwards;
}
@keyframes fadein {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.timeline-date {
  font-weight: bold;
  color: #0d6efd;
}
.timeline-doc {
  margin-top: 5px;
}
.badge-warning { background-color: #ffc107 !important; color: #212529; }
</style>

<div class="text-center mb-5">
    <h1 class="text-primary fw-bold">
        <?= $role === 'admin' ? 'Tableau de bord RH - Administrateur' : 'Mon espace RH personnel' ?>
    </h1>
    <p class="lead">Connecté en tant que <strong><?= $role ?></strong> (ID : <?= $_SESSION['user_id'] ?>)</p>
</div>

<div class="row g-4 justify-content-center">
<?php if ($role === 'admin'): ?>

    <!-- Cartes admin -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-primary shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fa fa-user-plus fa-2x text-primary mb-3"></i>
                <h5 class="card-title">Créer un utilisateur</h5>
                <p class="card-text">Ajoutez un nouveau compte RH ou secouriste.</p>
                <a href="create_user.php" class="btn btn-primary btn-sm">Ajouter</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-success shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fa fa-users fa-2x text-success mb-3"></i>
                <h5 class="card-title">Utilisateurs enregistrés</h5>
                <p class="card-text">Consultez et gérez les comptes existants.</p>
                <a href="manage_users.php" class="btn btn-success btn-sm">Voir la liste</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-warning shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fa fa-user-slash fa-2x text-warning mb-3"></i>
                <h5 class="card-title">Comptes inactifs</h5>
                <p class="card-text">Consultez les comptes désactivés.</p>
                <a href="manage_users.php?is_active=0" class="btn btn-warning btn-sm">Afficher</a>
            </div>
        </div>
    </div>

<?php else: ?>

    <!-- Cartes utilisateur -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-info shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fa fa-id-badge fa-2x text-info mb-3"></i>
                <h5 class="card-title">Mon profil</h5>
                <p class="card-text">Accédez à vos informations personnelles.</p>
                <a href="user_profile.php?id=<?= $_SESSION['user_id'] ?>" class="btn btn-info text-white btn-sm">Voir mon profil</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-secondary shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fa fa-file-alt fa-2x text-secondary mb-3"></i>
                <h5 class="card-title">Mes documents</h5>
                <p class="card-text">Téléversez ou consultez vos fichiers RH.</p>
                <a href="user_profile.php?id=<?= $_SESSION['user_id'] ?>" class="btn btn-secondary btn-sm">Accéder</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-warning shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fa fa-key fa-2x text-warning mb-3"></i>
                <h5 class="card-title">Sécurité</h5>
                <p class="card-text">Modifier votre mot de passe.</p>
                <a href="change_password.php" class="btn btn-warning btn-sm">Modifier</a>
            </div>
        </div>
    </div>

<?php endif; ?>
</div>

<hr class="my-5">
<h4 class="text-primary"><i class="fa fa-clock me-2"></i>Documents à surveiller</h4>

<div class="timeline mt-4">
<?php
if ($role === 'admin') {
    $query = $pdo->query("SELECT d.*, u.firstname, u.lastname FROM documents d JOIN users u ON d.user_id = u.id WHERE d.valid_until IS NOT NULL ORDER BY d.valid_until ASC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? AND valid_until IS NOT NULL ORDER BY valid_until ASC");
    $stmt->execute([$_SESSION['user_id']]);
    $query = $stmt;
}

foreach ($query->fetchAll() as $doc):
    $date = new DateTime($doc['valid_until']);
    $today = new DateTime();
    $interval = $today->diff($date)->days;
    $badge = '';

    if ($date < $today) {
        $badge = '<span class="badge bg-danger ms-2">Expiré</span>';
    } elseif ($interval <= 30) {
        $badge = '<span class="badge bg-warning text-dark ms-2">À renouveler</span>';
    }

    $icon = match ($doc['doc_type']) {
        'Diplôme' => '🎓',
        'Attestation' => '📜',
        'Carte professionnelle' => '🪪',
        'Certificat médical' => '🩺',
        default => '📁'
    };

    $owner = $role === 'admin' ? ' de <strong>' . htmlspecialchars($doc['firstname'] . ' ' . $doc['lastname']) . '</strong>' : '';
?>
    <div class="timeline-item">
        <div class="timeline-date"><?= $date->format('d/m/Y') ?></div>
        <div class="timeline-doc"><?= $icon ?> <?= htmlspecialchars($doc['doc_type']) . $owner ?> <?= $badge ?></div>
    </div>
<?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
