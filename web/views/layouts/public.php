<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'SkillSwap') ?> — SkillSwap</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-skillswap sticky-top py-3">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?= APP_URL ?>/"><span class="swap-mark"><i class="fa-solid fa-right-left"></i></span>SkillSwap</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav1"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav1">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/catalogue">Catalogue</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/certificats/verifier">Vérifier un certificat</a></li>
        <?php if (\App\Core\Auth::check()): ?>
          <li class="nav-item"><a class="btn btn-navy btn-sm px-3" href="<?= APP_URL ?>/tableau-de-bord">Mon espace</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-outline-navy btn-sm px-3" href="<?= APP_URL ?>/connexion">Se connecter</a></li>
          <li class="nav-item"><a class="btn btn-navy btn-sm px-3" href="<?= APP_URL ?>/inscription">S'inscrire</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<?php require __DIR__ . '/../partials/flash.php'; ?>

<?= $content ?>

<footer class="site-footer py-5 mt-5">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-4">
        <div class="d-flex align-items-center mb-3"><span class="swap-mark"><i class="fa-solid fa-right-left"></i></span><span class="fw-bold text-white fs-5" style="font-family:'Fraunces',serif;">SkillSwap</span></div>
        <p class="small">Apprenez une compétence, transmettez la vôtre. SkillSwap met en relation des personnes qui échangent leur savoir-faire, sans argent et avec confiance. Une plateforme qui contribue à l'ODD 4 : éducation de qualité.</p>
      </div>
      <div class="col-md-4">
        <div class="text-white fw-semibold mb-2">Plateforme</div>
        <div class="small d-flex flex-column gap-2">
          <a href="<?= APP_URL ?>/catalogue">Catalogue</a>
          <a href="<?= APP_URL ?>/certificats/verifier">Vérifier un certificat</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-white fw-semibold mb-2">Compte</div>
        <div class="small d-flex flex-column gap-2">
          <a href="<?= APP_URL ?>/connexion">Connexion</a>
          <a href="<?= APP_URL ?>/inscription">Inscription</a>
        </div>
      </div>
    </div>
    <hr class="my-4" style="border-color:#2c4a6e;">
    <div class="small text-center">© 2026 SkillSwap — Tous droits réservés</div>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
