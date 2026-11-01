<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Administration') ?> — SkillSwap Admin</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function adminNavActive($path, $needle) { return str_contains($path, $needle) ? 'active' : ''; }
?>

<nav class="app-nav app-nav-admin p-3 d-none d-lg-block">
  <div class="brand d-flex align-items-center mb-4 px-2"><span class="swap-mark"><i class="fa-solid fa-right-left"></i></span>SkillSwap <span class="small fw-normal" style="color:#8a97a8">Admin</span></div>
  <div class="nav flex-column gap-1">
    <a class="nav-link d-flex align-items-center <?= adminNavActive($currentPath, '/admin') === 'active' && !str_contains($currentPath, '/admin/') ? 'active' : '' ?>" href="<?= APP_URL ?>/admin"><i class="fa-solid fa-gauge me-2"></i>Tableau de bord</a>
    <a class="nav-link d-flex align-items-center <?= adminNavActive($currentPath, 'utilisateurs') ?>" href="<?= APP_URL ?>/admin/utilisateurs"><i class="fa-solid fa-users me-2"></i>Utilisateurs</a>
    <a class="nav-link d-flex align-items-center <?= adminNavActive($currentPath, 'signalements') ?>" href="<?= APP_URL ?>/admin/signalements"><i class="fa-solid fa-flag me-2"></i>Signalements</a>
    <a class="nav-link d-flex align-items-center <?= adminNavActive($currentPath, 'certificats') ?>" href="<?= APP_URL ?>/admin/certificats"><i class="fa-solid fa-certificate me-2"></i>Certificats</a>
    <a class="nav-link d-flex align-items-center <?= adminNavActive($currentPath, 'statistiques') ?>" href="<?= APP_URL ?>/admin/statistiques"><i class="fa-solid fa-chart-line me-2"></i>Statistiques</a>
    <hr style="border-color:rgba(255,255,255,0.12)">
    <a class="nav-link d-flex align-items-center" href="<?= APP_URL ?>/"><i class="fa-solid fa-arrow-left me-2"></i>Retour au site</a>
    <a class="nav-link d-flex align-items-center" href="<?= APP_URL ?>/deconnexion"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Déconnexion</a>
  </div>
</nav>

<div class="app-content">
  <div class="topbar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-navy d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavAdmin"><i class="fa-solid fa-bars"></i></button>
      <h1 class="h5 mb-0"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
    </div>
    <div class="avatar-sm" style="background:var(--navy-dark)">AD</div>
  </div>

  <?php require __DIR__ . '/../partials/flash.php'; ?>

  <div class="p-4">
    <?= $content ?>
  </div>
</div>

<div class="offcanvas offcanvas-start" id="mobileNavAdmin" style="background:var(--navy-dark);">
  <div class="offcanvas-body p-3">
    <div class="nav flex-column gap-1">
      <a class="nav-link text-white" href="<?= APP_URL ?>/admin">Tableau de bord</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/admin/utilisateurs">Utilisateurs</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/admin/signalements">Signalements</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/admin/certificats">Certificats</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/admin/statistiques">Statistiques</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/deconnexion">Déconnexion</a>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
