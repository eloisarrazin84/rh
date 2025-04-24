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

<div class="text-center mb-5">
    <h1 class="text-primary fw-bold">
        <?= $role === 'admin' ? 'Tableau de bord RH - Administrateur' : 'Mon espace RH personnel' ?>
    </h1>
    <p class="lead">Connecté en tant que <strong><?= $role ?></strong> (ID : <?= $_SESSION['user_id'] ?>)</p>
</div>

<div class="row g-4 justify-content-center">
<?php if ($role === 'admin'): ?>

    <!-- Admin : Ajouter utilisateur -->
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

    <!-- Admin : Voir tous les utilisateurs -->
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

    <!-- Admin : Voir comptes inactifs -->
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

    <!-- Utilisateur : Voir son profil -->
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

    <!-- Utilisateur : Accès documents -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-secondary shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fa fa-file-alt fa-2x text-secondary mb-3"></i>
                <h5 class="card-title">Mes documents</h5>
                <p class="card-text">Téléversez ou consultez vos fichiers RH.</p>
                <a href="upload_documents.php" class="btn btn-secondary btn-sm">Accéder</a>
            </div>
        </div>
    </div>

    <!-- Utilisateur : Modifier mot de passe -->
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

<?php
$content = ob_get_clean();
include 'includes/layout.php';
