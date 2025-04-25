<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT firstname, lastname, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'RH Outdoor Secours' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<ul class="navbar-nav me-auto mb-2 mb-lg-0">

  <?php if (isset($_SESSION['user_id'])): ?>
    <li class="nav-item">
      <a class="nav-link" href="dashboard.php"><i class="fa fa-home me-1"></i>Dashboard</a>
    </li>

    <?php if ($_SESSION['role'] === 'admin'): ?>
      <!-- Section admin -->
      <li class="nav-item">
        <a class="nav-link" href="create_user.php"><i class="fa fa-user-plus me-1"></i>Créer utilisateur</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="manage_users.php"><i class="fa fa-users me-1"></i>Gérer utilisateurs</a>
      </li>
    <?php endif; ?>

    <!-- Section utilisateur -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="profilMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-user-circle me-1"></i>Mon profil RH
      </a>
      <ul class="dropdown-menu" aria-labelledby="profilMenu">
        <li><a class="dropdown-item" href="user_profile.php?id=<?= $_SESSION['user_id'] ?>"><i class="fa fa-id-badge me-2"></i>Voir mon profil</a></li>
        <li><a class="dropdown-item" href="edit_profile.php?id=<?= $_SESSION['user_id'] ?>"><i class="fa fa-pen me-2"></i>Modifier mes infos</a></li>
        <li><a class="dropdown-item" href="change_password.php"><i class="fa fa-key me-2"></i>Changer mot de passe</a></li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link text-danger" href="logout.php"><i class="fa fa-power-off me-1"></i>Déconnexion</a>
    </li>

  <?php else: ?>
    <li class="nav-item">
      <a class="nav-link" href="login.php"><i class="fa fa-lock me-1"></i>Connexion</a>
    </li>
  <?php endif; ?>
</ul>

      <?php if (isset($_SESSION['user_id'])): ?>
<?php if (isset($currentUser)): ?>
    <div class="dropdown ms-auto">
        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
<img src="<?= $currentUser['avatar'] ? 'uploads/avatars/' . $currentUser['avatar'] : 'img/default_avatar.png' ?>"
     alt="avatar"
     width="32"
     height="32"
     class="rounded-circle me-2">
            <span><?= htmlspecialchars($currentUser['firstname'] . ' ' . $currentUser['lastname']) ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="user_profile.php?id=<?= $_SESSION['user_id'] ?>"><i class="fa fa-id-badge me-2"></i>Mon profil</a></li>
            <li><a class="dropdown-item" href="change_password.php"><i class="fa fa-key me-2"></i>Mot de passe</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa fa-power-off me-2"></i>Déconnexion</a></li>
        </ul>
    </div>
<?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Contenu principal -->
<main class="container mb-5">
  <?= $content ?>
</main>

<!-- Footer -->
<footer class="bg-light text-center text-muted py-3 border-top">
  <p class="mb-0">&copy; <?= date('Y') ?> Outdoor Secours – Tous droits réservés</p>
</footer>

<!-- Scripts JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="statusToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage">Action réussie.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
    </div>
  </div>
</div>
<?php if (isset($_SESSION['toast'])): ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const toastBox = document.createElement('div');
            toastBox.className = 'position-fixed bottom-0 end-0 p-3';
            toastBox.style.zIndex = 9999;

            toastBox.innerHTML = `
                <div class="toast text-bg-<?= $_SESSION['toast']['type'] ?> border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><?= $_SESSION['toast']['message'] ?></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            document.body.appendChild(toastBox);
            setTimeout(() => toastBox.remove(), 3000);
        });
    </script>
    <?php unset($_SESSION['toast']); ?>
<?php endif; ?>
</body>
</html>
