<?php
require 'includes/config.php';
$pageTitle = "Liste des utilisateurs";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
ob_start();
?>

<h2 class="text-primary mb-4">Utilisateurs enregistrés</h2>

<!-- Formulaire de recherche et filtres -->
<form method="GET" id="user-filter-form" class="row g-3 mb-4">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Recherche..." value="">
    </div>
    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value="">Tous les rôles</option>
            <option value="user">Utilisateur</option>
            <option value="admin">Administrateur</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="is_active" class="form-select">
            <option value="">Tous les statuts</option>
            <option value="1">Actifs</option>
            <option value="0">Inactifs</option>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </div>
</form>

<!-- Conteneur pour affichage AJAX -->
<div id="user-list">
    <?php include 'ajax_users.php'; ?>
</div>

<!-- Script AJAX -->
<script>
document.querySelector('#user-filter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const data = new URLSearchParams(new FormData(form));

    fetch('ajax_users.php?' + data)
        .then(res => res.text())
        .then(html => {
            document.querySelector('#user-list').innerHTML = html;
        });
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
