<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'SkillSwap') ?> — SkillSwap</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php
use App\Core\Auth;
use App\Models\Notification;
$moi = Auth::user();
$nbNotifNonLues = Auth::check() ? (new Notification())->nombreNonLues(Auth::id()) : 0;
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function navActive($path, $needle) { return str_contains($path, $needle) ? 'active' : ''; }
?>

<nav class="app-nav p-3 d-none d-lg-block">
  <a href="<?= APP_URL ?>/" class="brand d-flex align-items-center mb-4 px-2 text-decoration-none"><span class="swap-mark"><i class="fa-solid fa-right-left"></i></span>SkillSwap</a>
  <div class="nav flex-column gap-1">
    <a class="nav-link <?= navActive($currentPath, 'tableau-de-bord') ?>" href="<?= APP_URL ?>/tableau-de-bord"><i class="fa-solid fa-gauge me-2"></i>Tableau de bord</a>
    <a class="nav-link <?= navActive($currentPath, 'mes-competences') ?>" href="<?= APP_URL ?>/mes-competences"><i class="fa-solid fa-layer-group me-2"></i>Mes compétences</a>
    <a class="nav-link <?= navActive($currentPath, 'rechercher') ?>" href="<?= APP_URL ?>/rechercher"><i class="fa-solid fa-magnifying-glass me-2"></i>Rechercher</a>
    <a class="nav-link <?= navActive($currentPath, 'demandes-echange') ?>" href="<?= APP_URL ?>/demandes-echange"><i class="fa-solid fa-right-left me-2"></i>Demandes d'échange</a>
    <a class="nav-link <?= navActive($currentPath, 'seances') ?>" href="<?= APP_URL ?>/seances"><i class="fa-solid fa-calendar-days me-2"></i>Séances</a>
    <a class="nav-link <?= navActive($currentPath, 'messagerie') ?>" href="<?= APP_URL ?>/messagerie"><i class="fa-solid fa-comments me-2"></i>Messagerie</a>
    <a class="nav-link <?= navActive($currentPath, 'points') ?>" href="<?= APP_URL ?>/points"><i class="fa-solid fa-coins me-2"></i>Points &amp; badges</a>
    <a class="nav-link <?= navActive($currentPath, 'certificats') ?>" href="<?= APP_URL ?>/certificats"><i class="fa-solid fa-award me-2"></i>Certificats</a>
    <a class="nav-link <?= navActive($currentPath, 'notifications') ?>" href="<?= APP_URL ?>/notifications"><i class="fa-solid fa-bell me-2"></i>Notifications
      <?php if ($nbNotifNonLues > 0): ?><span class="badge-count"><?= $nbNotifNonLues ?></span><?php endif; ?>
    </a>
    <hr style="border-color:rgba(255,255,255,0.12)">
    <a class="nav-link <?= navActive($currentPath, 'profil/modifier') ?>" href="<?= APP_URL ?>/profil/modifier"><i class="fa-solid fa-gear me-2"></i>Paramètres</a>
    <a class="nav-link" href="<?= APP_URL ?>/deconnexion"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Déconnexion</a>
  </div>
</nav>

<div class="app-content">
  <div class="topbar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-navy d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav"><i class="fa-solid fa-bars"></i></button>
      <div>
        <h1 class="h5 mb-0"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <?php if ($moi && isset($moi['soldePoints'])): ?>
        <div class="points-chip"><i class="fa-solid fa-coins" style="color:var(--gold)"></i><span class="mono"><?= (int) $moi['soldePoints'] ?> pts</span></div>
      <?php endif; ?>
      <a href="<?= APP_URL ?>/notifications" class="position-relative text-decoration-none"><i class="fa-solid fa-bell fs-5" style="color:var(--navy)"></i><?php if ($nbNotifNonLues > 0): ?><span class="notif-dot"></span><?php endif; ?></a>
     <?php if (!empty($moi['photoProfil'])): ?>
  <img src="<?= APP_URL ?>/<?= htmlspecialchars($moi['photoProfil']) ?>" alt="" class="avatar-sm" style="object-fit:cover;">
<?php else: ?>
  <div class="avatar-sm"><?= $moi ? strtoupper(mb_substr($moi['prenom'] ?? 'U', 0, 1) . mb_substr($moi['nom'] ?? '', 0, 1)) : '?' ?></div>
<?php endif; ?>
    </div>
  </div>

  <?php require __DIR__ . '/../partials/flash.php'; ?>

  <div class="p-4">
    <?= $content ?>
  </div>
</div>

<div class="offcanvas offcanvas-start" id="mobileNav" style="background:var(--navy);">
  <div class="offcanvas-body p-3">
    <div class="nav flex-column gap-1">
      <a class="nav-link text-white" href="<?= APP_URL ?>/tableau-de-bord">Tableau de bord</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/mes-competences">Mes compétences</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/rechercher">Rechercher</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/demandes-echange">Demandes d'échange</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/seances">Séances</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/messagerie">Messagerie</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/points">Points &amp; badges</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/certificats">Certificats</a>
      <a class="nav-link text-white" href="<?= APP_URL ?>/deconnexion">Déconnexion</a>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
