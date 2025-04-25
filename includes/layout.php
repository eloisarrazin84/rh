<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/config.php';

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT firstname, lastname, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'RH Outdoor Secours' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- Navbar principale -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="dashboard.php">
      <i class="fa-solid fa-shield-halved me-2"></i>Outdoor Secours
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa fa-home me-1"></i>Dashboard</a></li>

          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="create_user.php"><i class="fa fa-user-plus me-1"></i>Créer utilisateur</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fa fa-users me-1"></i>Gérer utilisateurs</a></li>
          <?php endif; ?>
      </ul>

      <!-- Notifications -->
      <ul class="navbar-nav align-items-center">
        <li class="nav-item dropdown me-3">
          <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
            <i class="fa fa-bell fa-lg"></i>
            <?php if (!empty($_SESSION['notifications'])): ?>
              <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 300px; max-height: 300px; overflow-y: auto;">
            <li class="dropdown-header fw-bold text-primary">Notifications</li>
            <?php if (!empty($_SESSION['notifications'])): ?>
              <?php foreach ($_SESSION['notifications'] as $notif): ?>
                <li class="px-3 py-2 small border-bottom">
                  <i class="<?= $notif['icon'] ?? 'fa fa-info-circle' ?> me-2 text-<?= $notif['type'] ?? 'secondary' ?>"></i>
                  <?= htmlspecialchars($notif['message']) ?>
                  <div class="text-muted small"><?= $notif['time'] ?? '' ?></div>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="px-3 py-2 text-muted small">Aucune notification.</li>
            <?php endif; ?>
          </ul>
        </li>

        <!-- Avatar / Dropdown utilisateur -->
        <?php if ($currentUser): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
              <img src="<?= $currentUser['avatar'] ? 'uploads/avatars/' . $currentUser['avatar'] : 'img/default_avatar.png' ?>" width="32" height="32" class="rounded-circle me-2" alt="Avatar">
              <?= htmlspecialchars($currentUser['firstname']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="user_profile.php?id=<?= $_SESSION['user_id'] ?>"><i class="fa fa-user me-2"></i>Mon profil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa fa-power-off me-2"></i>Déconnexion</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Contenu principal -->
<main class="container my-4">
  <?= $content ?>
</main>

<!-- Footer -->
<footer class="bg-light text-center text-muted py-3 border-top">
  <p class="mb-0">&copy; <?= date('Y') ?> Outdoor Secours – Tous droits réservés</p>
</footer>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toast -->
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
    setTimeout(() => toastBox.remove(), 4000);
  });
</script>
<?php unset($_SESSION['toast']); endif; ?>

</body>
</html>
